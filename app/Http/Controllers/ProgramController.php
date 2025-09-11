<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProgramController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Handle case where user doesn't have company_id set
        if (!$user->company_id) {
            $emptyPrograms = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );

            return view('programs.index', [
                'programs' => $emptyPrograms,
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'draft' => 0,
                    'completed' => 0,
                ],
                'message' => 'Please contact your administrator to assign you to a company.'
            ]);
        }

        $query = Program::with(['company', 'coordinator', 'creator'])
            ->where('company_id', $user->company_id);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('program_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('eti_eligible')) {
            $query->where('eti_eligible_program', request('eti_eligible') === 'true');
        }

        $programs = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Program::where('company_id', $user->company_id)->count(),
            'active' => Program::where('company_id', $user->company_id)->active()->count(),
            'draft' => Program::where('company_id', $user->company_id)->status('draft')->count(),
            'completed' => Program::where('company_id', $user->company_id)->status('completed')->count(),
        ];

        return view('programs.index', compact('programs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $coordinators = User::where('company_id', Auth::user()->company_id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['hr_manager', 'company_admin', 'admin']);
            })
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        // Get program types
        $programTypes = \App\Models\ProgramType::orderBy('name')->get();

        return view('programs.create', compact('coordinators', 'programTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'program_code' => 'nullable|string|max:50|unique:programs',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_rate' => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|in:daily,weekly,monthly',
            'payment_day_of_month' => 'nullable|integer|between:1,31',
            'section_12h_eligible' => 'boolean',
            'section_12h_contract_number' => 'nullable|string|max:50',
            'section_12h_start_date' => 'nullable|date',
            'section_12h_end_date' => 'nullable|date|after:section_12h_start_date',
            'section_12h_allowance' => 'nullable|numeric|min:0',
            'eti_eligible_program' => 'boolean',
            'eti_category' => 'nullable|in:youth,disabled,other',
            'nqf_level' => 'nullable|integer|between:1,10',
            'saqa_id' => 'nullable|string|max:20',
            'qualification_title' => 'nullable|string|max:255',
            'location_type' => 'nullable|in:onsite,online,hybrid',
            'venue' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'max_learners' => 'nullable|integer|min:1',
            'min_learners' => 'nullable|integer|min:1',
            'bbbee_category' => 'nullable|in:Cat A,Cat B\\, C\\, D,Cat E',
            'is_client_hosting' => 'nullable|in:Yes,No,Maybe',
            'specific_requirements' => 'nullable|string',
            'learner_retention_rate' => 'nullable|integer|min:0|max:100',
            'completion_rate' => 'nullable|integer|min:0|max:100',
            'placement_rate' => 'nullable|integer|min:0|max:100',
            'coordinator_id' => 'nullable|exists:users,id',
            'program_type_id' => 'required|exists:program_types,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('programs/images', $filename, 'public');
            $validated['image'] = $path;
        }

        $validated['creator_id'] = Auth::id();
        $validated['company_id'] = Auth::user()->company_id;

        $program = Program::create($validated);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): View
    {
        // Set the current company in session for tenant scope
        session(['current_company_id' => $program->company_id]);

        // Restrict access to users from the same company group
        $user = Auth::user();
        $userCompany = $user->company;
        $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanyIds->contains($program->company_id)) {
            abort(403, 'Unauthorized: You do not have access to this program.');
        }

        // Load relationships
        $program->load(['company', 'coordinator', 'creator', 'approvedBy', 'programType', 'participants.user', 'leaveRequests.user']);

        // Get related schedules if they exist
        $schedules = collect(); // Empty collection for now
        try {
            if (class_exists('\App\Models\Schedule')) {
                $schedules = \App\Models\Schedule::where('program_id', $program->id)
                    ->get();
            }
        } catch (\Exception $e) {
            // Schedule model might not exist yet, that's ok
        }

        // Build stats array with safe fallbacks
        $stats = [
            'total_schedules' => $schedules->count(),
            'active_schedules' => $schedules->where('status', 'scheduled')->count(),
            'completed_schedules' => $schedules->where('status', 'completed')->count(),
            'total_learners' => $program->enrolled_count ?? 0,
            'remaining_spots' => method_exists($program, 'getRemainingSpots') ? $program->getRemainingSpots() : 0,
            'total_value' => method_exists($program, 'getTotalValue') ? $program->getTotalValue() : 0,
            'daily_payment' => method_exists($program, 'getTotalDailyPayment') ? $program->getTotalDailyPayment() : 0,
            'average_attendance' => $schedules->avg('attendance_rate') ?? 0,
            'completion_rate' => $program->completion_rate ?? 0,
        ];

        return view('programs.show', compact('program', 'stats', 'schedules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program): View
    {
        $this->authorize('update', $program);

        $coordinators = User::where('company_id', Auth::user()->company_id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['hr_manager', 'company_admin', 'admin']);
            })
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return view('programs.edit', compact('program', 'coordinators'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program): RedirectResponse
    {
        $this->authorize('update', $program);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'program_code' => "required|string|max:50|unique:programs,program_code,{$program->id}",
            'description' => 'nullable|string',
            'coordinator_id' => 'nullable|exists:users,id',
            'nqf_level' => 'required|integer|between:1,10',
            'saqa_id' => 'nullable|string|max:20',
            'qualification_title' => 'nullable|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'duration_weeks' => 'nullable|integer|min:1',
            'total_training_days' => 'nullable|integer|min:1',
            'daily_rate' => 'required|numeric|min:0',
            'monthly_stipend' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'accommodation_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'other_allowance_description' => 'nullable|string|max:255',
            'payment_frequency' => 'required|in:daily,weekly,monthly',
            'payment_day_of_month' => 'nullable|integer|between:1,31',
            'section_12h_eligible' => 'boolean',
            'section_12h_contract_number' => 'nullable|string|max:50',
            'section_12h_start_date' => 'nullable|date',
            'section_12h_end_date' => 'nullable|date|after:section_12h_start_date',
            'section_12h_allowance' => 'nullable|numeric|min:0',
            'eti_eligible_program' => 'boolean',
            'eti_category' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'enrollment_start_date' => 'nullable|date',
            'enrollment_end_date' => 'nullable|date|after:enrollment_start_date',
            'max_learners' => 'required|integer|min:1',
            'min_learners' => 'nullable|integer|min:1',
            'location_type' => 'required|in:onsite,offsite,online,hybrid',
            'venue' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'bbbee_category' => 'nullable|string|max:50',
            'specific_requirements' => 'nullable|string',
        ]);

        $program->update($validated);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $this->authorize('delete', $program);

        if ($program->schedules()->count() > 0) {
            return redirect()->route('programs.index')
                ->with('error', 'Cannot delete program with existing schedules.');
        }

        if ($program->enrolled_count > 0) {
            return redirect()->route('programs.index')
                ->with('error', 'Cannot delete program with enrolled learners.');
        }

        $program->delete();

        return redirect()->route('programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Duplicate a program.
     */
    public function duplicate(Program $program): RedirectResponse
    {
        $this->authorize('create', Program::class);

        $newProgram = $program->replicate();
        $newProgram->program_code = "{$program->program_code}-COPY-" . now()->format('Ymd');
        $newProgram->title = "{$program->title} (Copy)";
        $newProgram->status = 'draft';
        $newProgram->creator_id = Auth::id();
        $newProgram->enrolled_count = 0;
        $newProgram->is_approved = false;
        $newProgram->approved_at = null;
        $newProgram->approved_by = null;
        $newProgram->save();

        return redirect()->route('programs.edit', $newProgram)
            ->with('success', 'Program duplicated successfully. Please review and update the details.');
    }

    /**
     * Activate a program.
     */
    public function activate(Program $program): RedirectResponse
    {
        $this->authorize('manage', $program);

        if (!$program->is_approved) {
            return redirect()->back()
                ->with('error', 'Program must be approved before it can be activated.');
        }

        $program->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Program activated successfully.');
    }

    /**
     * Deactivate a program.
     */
    public function deactivate(Program $program): RedirectResponse
    {
        $this->authorize('manage', $program);

        $program->update(['status' => 'inactive']);

        return redirect()->back()
            ->with('success', 'Program deactivated successfully.');
    }

    /**
     * Approve a program.
     */
    public function approve(Program $program): RedirectResponse
    {
        $this->authorize('approve', $program);

        $program->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'status' => $program->status === 'pending_approval' ? 'approved' : $program->status,
        ]);

        return redirect()->back()
            ->with('success', 'Program approved successfully.');
    }

    /**
     * Show program schedules management page.
     */
    public function schedules(Program $program): View
    {
        $this->authorize('view', $program);

        // Get schedules with relationships
        $schedules = collect();
        try {
            if (class_exists('\App\Models\Schedule')) {
                $schedules = \App\Models\Schedule::where('program_id', $program->id)
                    ->with(['instructor', 'attendanceRecords'])
                    ->orderBy('date', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            // Schedule model might not exist yet
        }

        $stats = [
            'total_schedules' => $schedules->count(),
            'completed_schedules' => $schedules->where('status', 'completed')->count(),
            'upcoming_schedules' => $schedules->where('status', 'scheduled')->count(),
            'average_attendance' => $schedules->avg('attendance_rate') ?? 0,
        ];

        return view('programs.schedules', compact('program', 'schedules', 'stats'));
    }

    /**
     * Show program progress tracking page.
     */
    public function progress(Program $program): View
    {
        $this->authorize('view', $program);

        $program->load(['participants.user', 'schedules']);

        // Calculate progress statistics
        $totalParticipants = $program->participants->count();
        $activeParticipants = $program->participants->where('status', 'active')->count();
        $completedParticipants = $program->participants->where('status', 'completed')->count();
        $droppedParticipants = $program->participants->where('status', 'dropped')->count();

        $stats = [
            'total_participants' => $totalParticipants,
            'active_participants' => $activeParticipants,
            'completed_participants' => $completedParticipants,
            'dropped_participants' => $droppedParticipants,
            'completion_rate' => $totalParticipants > 0 ? round(($completedParticipants / $totalParticipants) * 100, 1) : 0,
            'retention_rate' => $totalParticipants > 0 ? round((($activeParticipants + $completedParticipants) / $totalParticipants) * 100, 1) : 0,
        ];

        return view('programs.progress', compact('program', 'stats'));
    }

    /**
     * Generate revenue report for the program.
     */
    public function revenueReport(Program $program): View
    {
        $this->authorize('view', $program);

        // Calculate revenue data
        $totalValue = $program->getTotalValue() ?? 0;
        $dailyRate = $program->daily_rate ?? 0;
        $transportAllowance = $program->transport_allowance ?? 0;
        $participantCount = $program->participants->where('status', 'active')->count();

        // Get payslips data if available
        $payslipsData = [];
        try {
            if (class_exists('\App\Models\Payslip')) {
                $payslips = \App\Models\Payslip::where('program_id', $program->id)
                    ->with('user')
                    ->get();
                
                $payslipsData = [
                    'total_paid' => $payslips->sum('net_amount'),
                    'total_payslips' => $payslips->count(),
                    'pending_payslips' => $payslips->where('status', 'pending')->count(),
                    'approved_payslips' => $payslips->where('status', 'approved')->count(),
                ];
            }
        } catch (\Exception $e) {
            // Payslip model might not exist
        }

        $revenueStats = [
            'program_value' => $totalValue,
            'daily_rate' => $dailyRate,
            'transport_allowance' => $transportAllowance,
            'active_participants' => $participantCount,
            'monthly_cost' => ($dailyRate + $transportAllowance) * $participantCount * 22, // Assuming 22 working days
            'payslips_data' => $payslipsData,
        ];

        return view('programs.revenue-report', compact('program', 'revenueStats'));
    }

    /**
     * Generate client pack for the program.
     */
    public function clientPack(Program $program): View
    {
        $this->authorize('view', $program);

        $program->load(['company', 'coordinator', 'programType', 'participants.user']);

        // Prepare client pack data
        $clientPackData = [
            'program_overview' => $program,
            'participant_list' => $program->participants->where('status', 'active'),
            'schedule_summary' => [], // Will be populated if schedules exist
            'compliance_info' => [
                'section_12h_eligible' => $program->section_12h_eligible,
                'eti_eligible' => $program->eti_eligible_program,
                'section_12h_contract' => $program->section_12h_contract_number,
            ],
        ];

        return view('programs.client-pack', compact('program', 'clientPackData'));
    }

    /**
     * Add a participant to the program.
     */
    public function addParticipant(Request $request, Program $program)
    {
        $this->authorize('update', $program);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'enrolled_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,completed,dropped',
        ]);

        // Check if user is already enrolled
        $existingParticipant = $program->participants()->where('user_id', $request->user_id)->first();
        if ($existingParticipant) {
            return response()->json([
                'success' => false,
                'message' => 'User is already enrolled in this program.',
            ], 422);
        }

        // Check program capacity
        if ($program->max_learners && $program->participants->count() >= $program->max_learners) {
            return response()->json([
                'success' => false,
                'message' => 'Program has reached maximum capacity.',
            ], 422);
        }

        try {
            // Add participant through the relationship
            $program->participants()->attach($request->user_id, [
                'enrolled_date' => $request->enrolled_date ?? now(),
                'status' => $request->status ?? 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Participant added successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add participant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update participant status.
     */
    public function updateParticipantStatus(Request $request, Program $program, User $user)
    {
        $this->authorize('update', $program);

        $request->validate([
            'status' => 'required|in:active,inactive,completed,dropped',
            'reason' => 'nullable|string|max:500',
        ]);

        // Check if user is enrolled in this program
        $participant = $program->participants()->where('user_id', $user->id)->first();
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'User is not enrolled in this program.',
            ], 404);
        }

        try {
            // Update participant status
            $program->participants()->updateExistingPivot($user->id, [
                'status' => $request->status,
                'status_reason' => $request->reason,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Participant status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update participant status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show participant details.
     */
    public function showParticipant(Program $program, User $user): View
    {
        $this->authorize('view', $program);

        // Check if user is enrolled in this program
        $participant = $program->participants()->where('user_id', $user->id)->first();
        if (!$participant) {
            abort(404, 'Participant not found in this program.');
        }

        $user->load(['programs', 'leaveRequests' => function($query) use ($program) {
            $query->where('program_id', $program->id);
        }]);

        // Get participant's attendance data if available
        $attendanceData = [];
        try {
            if (class_exists('\App\Models\AttendanceRecord')) {
                $attendanceRecords = \App\Models\AttendanceRecord::where('user_id', $user->id)
                    ->whereHas('schedule', function($query) use ($program) {
                        $query->where('program_id', $program->id);
                    })
                    ->with('schedule')
                    ->get();

                $attendanceData = [
                    'total_sessions' => $attendanceRecords->count(),
                    'attended_sessions' => $attendanceRecords->where('status', 'present')->count(),
                    'missed_sessions' => $attendanceRecords->where('status', 'absent')->count(),
                    'attendance_rate' => $attendanceRecords->count() > 0 ? 
                        round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1) : 0,
                ];
            }
        } catch (\Exception $e) {
            // AttendanceRecord model might not exist
        }

        return view('programs.participant-details', compact('program', 'user', 'participant', 'attendanceData'));
    }

    /**
     * Upload a document for the program.
     */
    public function uploadDocument(Request $request, Program $program)
    {
        $this->authorize('update', $program);

        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'document_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('program-documents/' . $program->id, $filename, 'public');

            // Here you would typically save document info to a database table
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'document' => [
                    'filename' => $filename,
                    'path' => $path,
                    'type' => $request->document_type,
                    'description' => $request->description,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage(),
            ], 500);
        }
    }
}
