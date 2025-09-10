<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Schedule::with(['program', 'instructor', 'creator'])
            ->where('company_id', Auth::user()->company_id);
            
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('session_code', 'like', "%{$search}%")
                  ->orWhere('module_name', 'like', "%{$search}%")
                  ->orWhereHas('program', function($programQuery) use ($search) {
                      $programQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('session_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('session_date', '<=', $request->date_to);
        }
        
        if ($request->has('online')) {
            $query->where('is_online', $request->online === 'true');
        }

        $schedules = $query->latest('session_date')->paginate(15)->withQueryString();

        $programs = Program::where('company_id', Auth::user()->company_id)
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        $stats = [
            'total' => Schedule::where('company_id', Auth::user()->company_id)->count(),
            'today' => Schedule::where('company_id', Auth::user()->company_id)->today()->count(),
            'scheduled' => Schedule::where('company_id', Auth::user()->company_id)->byStatus('scheduled')->count(),
            'in_progress' => Schedule::where('company_id', Auth::user()->company_id)->byStatus('in_progress')->count(),
            'completed' => Schedule::where('company_id', Auth::user()->company_id)->byStatus('completed')->count(),
        ];

        return view('schedules.index', compact('schedules', 'programs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $programs = Program::where('company_id', Auth::user()->company_id)
            ->active()
            ->orderBy('title')
            ->get();
            
        $instructors = User::where('company_id', Auth::user()->company_id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['instructor', 'hr_manager', 'company_admin', 'admin']);
            })
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return view('schedules.create', compact('programs', 'instructors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'instructor_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'module_name' => 'nullable|string|max:255',
            'unit_standard' => 'nullable|string|max:255',
            'learning_outcomes' => 'nullable|string',
            'assessment_criteria' => 'nullable|string',
            'required_materials' => 'nullable|array',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'room_number' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'campus' => 'nullable|string|max:100',
            'is_online' => 'boolean',
            'meeting_url' => 'nullable|url',
            'meeting_id' => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:50',
            'platform' => 'nullable|string|max:50',
            'venue_latitude' => 'nullable|numeric|between:-90,90',
            'venue_longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'nullable|integer|min:10|max:1000',
            'expected_attendees' => 'required|integer|min:1',
            'check_in_opens_at' => 'nullable|date_format:H:i',
            'check_in_closes_at' => 'nullable|date_format:H:i',
            'allow_late_check_in' => 'boolean',
            'late_threshold_minutes' => 'nullable|integer|min:5|max:120',
            'session_type' => 'required|in:theory,practical,assessment,workshop,field_work',
            'send_reminders' => 'boolean',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'scheduled';
        $validated['qr_code_active'] = true;
        $validated['actual_attendees'] = 0;

        // Set QR code validity window
        $validated['qr_code_valid_from'] = now()->addHours(1); // Active 1 hour before
        $validated['qr_code_valid_until'] = now()->addDay(); // Valid for 24 hours

        $schedule = Schedule::create($validated);

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): View
    {
        $this->authorize('view', $schedule);
        
        $schedule->load(['program', 'instructor', 'creator', 'attendanceRecords.user']);

        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule): View
    {
        $this->authorize('update', $schedule);
        
        $programs = Program::where('company_id', Auth::user()->company_id)
            ->active()
            ->orderBy('title')
            ->get();
            
        $instructors = User::where('company_id', Auth::user()->company_id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['instructor', 'hr_manager', 'company_admin', 'admin']);
            })
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return view('schedules.edit', compact('schedule', 'programs', 'instructors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'instructor_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'module_name' => 'nullable|string|max:255',
            'unit_standard' => 'nullable|string|max:255',
            'learning_outcomes' => 'nullable|string',
            'assessment_criteria' => 'nullable|string',
            'required_materials' => 'nullable|array',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'room_number' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:100',
            'campus' => 'nullable|string|max:100',
            'is_online' => 'boolean',
            'meeting_url' => 'nullable|url',
            'meeting_id' => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:50',
            'platform' => 'nullable|string|max:50',
            'venue_latitude' => 'nullable|numeric|between:-90,90',
            'venue_longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'nullable|integer|min:10|max:1000',
            'expected_attendees' => 'required|integer|min:1',
            'check_in_opens_at' => 'nullable|date_format:H:i',
            'check_in_closes_at' => 'nullable|date_format:H:i',
            'allow_late_check_in' => 'boolean',
            'late_threshold_minutes' => 'nullable|integer|min:5|max:120',
            'session_type' => 'required|in:theory,practical,assessment,workshop,field_work',
            'send_reminders' => 'boolean',
        ]);

        $schedule->update($validated);

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        $this->authorize('delete', $schedule);
        
        if ($schedule->attendanceRecords()->count() > 0) {
            return redirect()->route('schedules.index')
                ->with('error', 'Cannot delete schedule with existing attendance records.');
        }

        if ($schedule->status === 'in_progress' || $schedule->status === 'completed') {
            return redirect()->route('schedules.index')
                ->with('error', 'Cannot delete schedule that is in progress or completed.');
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    /**
     * Start a scheduled session.
     */
    public function start(Schedule $schedule): RedirectResponse
    {
        $this->authorize('manage', $schedule);
        
        if ($schedule->status !== 'scheduled') {
            return redirect()->back()
                ->with('error', 'Only scheduled sessions can be started.');
        }

        $schedule->start();

        return redirect()->back()
            ->with('success', 'Session started successfully.');
    }

    /**
     * Complete an in-progress session.
     */
    public function complete(Schedule $schedule): RedirectResponse
    {
        $this->authorize('manage', $schedule);
        
        if ($schedule->status !== 'in_progress') {
            return redirect()->back()
                ->with('error', 'Only sessions in progress can be completed.');
        }

        $schedule->complete();

        return redirect()->back()
            ->with('success', 'Session completed successfully.');
    }

    /**
     * Cancel a scheduled session.
     */
    public function cancel(Request $request, Schedule $schedule): RedirectResponse
    {
        $this->authorize('manage', $schedule);
        
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($schedule->status === 'completed' || $schedule->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a completed or already cancelled session.');
        }

        $schedule->cancel(Auth::user(), $validated['reason']);

        return redirect()->back()
            ->with('success', 'Session cancelled successfully.');
    }

    /**
     * Generate QR code for a session.
     */
    public function qrCode(Schedule $schedule): View
    {
        $this->authorize('view', $schedule);
        
        if (!$schedule->qr_code_active) {
            abort(404, 'QR code is not active for this session.');
        }

        return view('schedules.qr-code', compact('schedule'));
    }
}
