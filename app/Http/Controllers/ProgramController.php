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

        // Get clients for the dropdown
        $clients = \App\Models\Client::where('company_id', Auth::user()->company_id)
            ->active()
            ->orderBy('name')
            ->get();

        $selectedClientId = $request->get('client_id');

        return view('programs.create', compact('coordinators', 'clients', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'program_code' => 'required|string|max:50|unique:programs',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
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

        $validated['company_id'] = Auth::user()->company_id;
        $validated['creator_id'] = Auth::id();
        $validated['status'] = 'draft';
        $validated['enrolled_count'] = 0;

        $program = Program::create($validated);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): View
    {
        $this->authorize('view', $program);

        $program->load(['company', 'coordinator', 'creator', 'approvedBy']);

        // Get related schedules (need to add this relationship to Program model)
        $schedules = \App\Models\Schedule::where('program_id', $program->id)
            ->with('attendanceRecords')
            ->get();

        $stats = [
            'total_schedules' => $schedules->count(),
            'active_schedules' => $schedules->where('status', 'scheduled')->count(),
            'completed_schedules' => $schedules->where('status', 'completed')->count(),
            'total_learners' => $program->enrolled_count,
            'remaining_spots' => $program->getRemainingSpots(),
            'total_value' => $program->getTotalValue(),
            'daily_payment' => $program->getTotalDailyPayment(),
            'average_attendance' => $schedules->avg('attendance_rate') ?? 0,
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
}
