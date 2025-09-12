<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\LeaveAccrual;
use App\Models\LeaveCarryOver;
use App\Models\Program;
use App\Models\User;
use App\Services\LeaveManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveManagementService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Display leave management dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get leave balance summary
        $balanceSummary = $this->leaveService->getLeaveBalanceSummary($user);

        // Get recent leave requests
        $recentRequests = LeaveRequest::where('user_id', $user->id)
            ->with(['leaveType', 'program', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get expiring carry over days
        $expiringCarryOver = LeaveCarryOver::getExpiringCarryOverDays($user->id, 30);

        // Get accrual summary
        $accrualSummary = LeaveAccrual::getAccrualSummary($user->id);

        return view('leave-requests.index', compact(
            'balanceSummary',
            'recentRequests',
            'expiringCarryOver',
            'accrualSummary'
        ));
    }

    /**
     * Show leave balance details.
     */
    public function balances(): View
    {
        $user = Auth::user();
        $balanceSummary = $this->leaveService->getLeaveBalanceSummary($user);
        $leaveTypes = LeaveType::getForRole($user->getRoleNames()->first() ?? 'learner');

        // Get detailed accrual history
        $accrualHistory = LeaveAccrual::where('user_id', $user->id)
            ->with('leaveType')
            ->orderBy('accrual_date', 'desc')
            ->limit(50)
            ->get();

        return view('leave-requests.balances', compact(
            'balanceSummary',
            'leaveTypes',
            'accrualHistory'
        ));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create(): View
    {
        $user = Auth::user();
        $programs = $user->programs()->where('programs.status', 'active')->get();
        $leaveTypes = LeaveType::getForRole($user->getRoleNames()->first() ?? 'learner');

        // Get current balances for each leave type
        $balances = [];
        foreach ($leaveTypes as $leaveType) {
            $balances[$leaveType->id] = LeaveAccrual::getCurrentBalance($user->id, $leaveType->id);
        }

        return view('leave-requests.create', compact('programs', 'leaveTypes', 'balances'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $program = Program::findOrFail($request->program_id);

        // Check if user is enrolled in the program
        if (!$program->participants()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['program_id' => 'You are not enrolled in this program.']);
        }

        // Prepare request data
        $requestData = [
            'user_id' => Auth::id(),
            'program_id' => $request->program_id,
            'company_id' => $program->company_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ];

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('medical-certificates', 'public');
            $requestData['medical_certificate_path'] = $path;
        }

        // Process leave request through service
        $result = $this->leaveService->processLeaveRequest($requestData);

        if (!$result['success']) {
            return back()->withErrors($result['errors'])->withInput();
        }

        $message = 'Leave request submitted successfully.';
        if (!empty($result['validation']['warnings'])) {
            $message .= ' Note: ' . implode(' ', $result['validation']['warnings']);
        }

        return redirect()->route('leave-requests.show', $result['leave_request'])
            ->with('success', $message);
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest): View
    {
        // Check access permissions
        if ($leaveRequest->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            abort(403, 'Unauthorized access to leave request.');
        }

        $leaveRequest->load(['user', 'program', 'company', 'leaveType', 'approvedBy']);

        // Get user's leave balance summary
        $balanceSummary = $this->leaveService->getLeaveBalanceSummary($leaveRequest->user);

        // Extract variables for view compatibility
        $user = $leaveRequest->user;
        $program = $leaveRequest->program;
        $leaveBalance = $balanceSummary['balance_record'];

        return view('leave-requests.show', compact('leaveRequest', 'balanceSummary', 'user', 'program', 'leaveBalance'));
    }

    /**
     * Show leave requests for management (HR/Managers).
     */
    public function manage(): View
    {
        if (!Auth::user()->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            abort(403, 'Unauthorized to manage leave requests.');
        }

        $user = Auth::user();
        $userCompany = $user->company;
        $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');

        // Get pending requests
        $pendingRequests = LeaveRequest::whereIn('company_id', $allowedCompanyIds)
            ->where('status', 'pending')
            ->with(['user', 'program', 'leaveType'])
            ->orderBy('submitted_at', 'asc')
            ->get();

        // Get recent decisions
        $recentDecisions = LeaveRequest::whereIn('company_id', $allowedCompanyIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['user', 'program', 'leaveType', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->limit(20)
            ->get();

        // Get statistics
        $stats = [
            'pending' => LeaveRequest::whereIn('company_id', $allowedCompanyIds)->where('status', 'pending')->count(),
            'approved_this_month' => LeaveRequest::whereIn('company_id', $allowedCompanyIds)
                ->where('status', 'approved')
                ->whereMonth('approved_at', Carbon::now()->month)
                ->count(),
            'total_this_year' => LeaveRequest::whereIn('company_id', $allowedCompanyIds)
                ->whereYear('start_date', Carbon::now()->year)
                ->count(),
        ];

        return view('leave-requests.manage', compact('pendingRequests', 'recentDecisions', 'stats'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if (!Auth::user()->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            abort(403, 'Unauthorized to approve leave requests.');
        }

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Leave request has already been processed.']);
        }

        $result = $this->leaveService->approveLeaveRequest($leaveRequest, Auth::user());

        if (!$result['success']) {
            return back()->withErrors(['approval' => $result['error']]);
        }

        return back()->with('success', "Leave request approved. {$result['days_deducted']} days deducted from balance.");
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if (!Auth::user()->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            abort(403, 'Unauthorized to reject leave requests.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'Leave request has already been processed.']);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Leave request rejected successfully.');
    }

    /**
     * Cancel a leave request (by the requester).
     */
    public function cancel(LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to cancel this leave request.');
        }

        if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
            return back()->withErrors(['status' => 'Cannot cancel this leave request.']);
        }

        // If approved, need to restore leave balance
        if ($leaveRequest->status === 'approved' && $leaveRequest->leaveType) {
            $leaveDays = $this->leaveService->calculateLeaveDays(
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->leaveType
            );

            // Restore the balance
            LeaveAccrual::createBonusAccrual(
                $leaveRequest->user_id,
                $leaveRequest->program_id,
                $leaveRequest->company_id,
                $leaveRequest->leave_type_id,
                $leaveDays,
                'leave_cancelled',
                "Leave cancelled: {$leaveRequest->start_date->format('Y-m-d')} to {$leaveRequest->end_date->format('Y-m-d')}"
            );
        }

        $leaveRequest->update([
            'status' => 'cancelled',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'rejection_reason' => 'Cancelled by employee',
        ]);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Show SARS leave report.
     */
    public function sarsReport(): View
    {
        if (!Auth::user()->hasAnyRole(['admin', 'hr_manager', 'company_admin'])) {
            abort(403, 'Unauthorized to view SARS reports.');
        }

        $year = request('year', Carbon::now()->year);
        $company = Auth::user()->company;

        $report = $this->leaveService->generateSARSLeaveReport($company, $year);

        return view('leave-requests.sars-report', compact('report', 'year'));
    }

    /**
     * Initialize leave balances for a user (admin function).
     */
    public function initializeBalances(Request $request): RedirectResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized to initialize leave balances.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $program = Program::findOrFail($request->program_id);
        $company = $program->company;

        $balance = $this->leaveService->initializeLeaveBalances($user, $program, $company);

        return back()->with('success', "Leave balances initialized for {$user->full_name} in {$program->title}.");
    }
}
