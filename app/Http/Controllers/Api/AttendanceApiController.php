<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Host;
use App\Models\Program;
use App\Models\ProgramLearner;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceApiController extends Controller
{
    /**
     * Check-in with QR code validation.
     */
    public function checkIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'program_id' => 'required|exists:programs,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $today = now()->toDateString();

        // Validate QR code and get host
        $location = null;
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $location = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
        }

        $qrValidation = Host::validateQRCode($request->qr_code, $location);

        if (!$qrValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $qrValidation['error'],
            ], 400);
        }

        $host = $qrValidation['host'];

        // Check if attendance record already exists for today
        $attendanceRecord = AttendanceRecord::where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->where('program_id', $request->program_id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendanceRecord) {
            $attendanceRecord = AttendanceRecord::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'program_id' => $request->program_id,
                'attendance_date' => $today,
                'status' => 'present',
                'is_payable' => true,
            ]);
        }

        // Update with check-in details
        $attendanceRecord->update([
            'check_in_time' => now(),
            'check_in_qr_code' => $request->qr_code,
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'check_in_location_name' => $host->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'data' => $attendanceRecord->fresh(),
        ]);
    }

    /**
     * Get available programs for a user.
     */
    public function getPrograms(): JsonResponse
    {
        $user = auth()->user();

        $programs = Program::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->select('id', 'program_name', 'program_type', 'daily_rate')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Get attendance summary for a user.
     */
    public function getAttendanceSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attendanceRecords = AttendanceRecord::where('user_id', $request->user_id)
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date])
            ->get();

        $summary = [
            'total_days' => $attendanceRecords->count(),
            'present_days' => $attendanceRecords->where('status', 'present')->count(),
            'late_days' => $attendanceRecords->where('status', 'late')->count(),
            'absent_days' => $attendanceRecords->whereIn('status', ['absent_unauthorized', 'absent_authorized'])->count(),
            'total_hours' => $attendanceRecords->sum('hours_worked'),
            'total_pay' => $attendanceRecords->where('is_payable', true)->sum('calculated_pay'),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get learner's current schedule.
     */
    public function getCurrentSchedule(Request $request): JsonResponse
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // Get learner's active programs
        $programs = Program::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        $schedules = [];

        foreach ($programs as $program) {
            // Get today's schedule for this program
            $todaySchedule = Schedule::where('program_id', $program->id)
                ->where('day_of_week', now()->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if ($todaySchedule) {
                $schedules[] = [
                    'program' => [
                        'id' => $program->id,
                        'title' => $program->title,
                        'program_code' => $program->program_code,
                        'daily_rate' => $program->daily_rate,
                    ],
                    'schedule' => [
                        'id' => $todaySchedule->id,
                        'day_of_week' => $todaySchedule->day_of_week,
                        'day_name' => $todaySchedule->day_name,
                        'start_time' => $todaySchedule->start_time,
                        'end_time' => $todaySchedule->end_time,
                        'break_duration' => $todaySchedule->break_duration,
                        'is_active' => $todaySchedule->is_active,
                    ],
                    'is_current_time' => $this->isWithinScheduleTime($currentTime, $todaySchedule->start_time, $todaySchedule->end_time),
                    'time_until_start' => $this->getTimeUntilStart($currentTime, $todaySchedule->start_time),
                    'time_until_end' => $this->getTimeUntilEnd($currentTime, $todaySchedule->end_time),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $today,
                'current_time' => $currentTime,
                'schedules' => $schedules,
                'has_schedule_today' => count($schedules) > 0,
            ],
        ]);
    }

    /**
     * Get available hosts for a program.
     */
    public function getHosts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $programId = $request->program_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Get hosts for the program
        $hosts = Host::where('program_id', $programId)
            ->where('is_active', true)
            ->get();

        $hostsData = [];

        foreach ($hosts as $host) {
            $hostData = [
                'id' => $host->id,
                'name' => $host->name,
                'location_name' => $host->location_name,
                'city' => $host->city,
                'province' => $host->province,
                'latitude' => $host->latitude,
                'longitude' => $host->longitude,
                'radius_meters' => $host->radius_meters,
                'qr_code' => $host->qr_code,
                'is_active' => $host->is_active,
            ];

            // If location is provided, calculate distance and check if within radius
            if ($latitude && $longitude) {
                $distance = $host->getDistance($latitude, $longitude);
                $hostData['distance_meters'] = round($distance, 2);
                $hostData['is_within_radius'] = $distance <= $host->radius_meters;
            }

            $hostsData[] = $hostData;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'program_id' => $programId,
                'hosts' => $hostsData,
                'total_hosts' => count($hostsData),
            ],
        ]);
    }

    /**
     * Get nearest host for a program.
     */
    public function getNearestHost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $programId = $request->program_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Get all active hosts for the program
        $hosts = Host::where('program_id', $programId)
            ->where('is_active', true)
            ->get();

        if ($hosts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active hosts found for this program',
            ], 404);
        }

        $nearestHost = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($hosts as $host) {
            $distance = $host->getDistance($latitude, $longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestHost = $host;
            }
        }

        $hostData = [
            'id' => $nearestHost->id,
            'name' => $nearestHost->name,
            'location_name' => $nearestHost->location_name,
            'city' => $nearestHost->city,
            'province' => $nearestHost->province,
            'latitude' => $nearestHost->latitude,
            'longitude' => $nearestHost->longitude,
            'radius_meters' => $nearestHost->radius_meters,
            'qr_code' => $nearestHost->qr_code,
            'distance_meters' => round($minDistance, 2),
            'is_within_radius' => $minDistance <= $nearestHost->radius_meters,
        ];

        return response()->json([
            'success' => true,
            'data' => $hostData,
        ]);
    }

    /**
     * Validate QR code and get host information.
     */
    public function validateQRCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $qrCode = $request->qr_code;
        $location = null;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $location = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
        }

        $validation = Host::validateQRCode($qrCode, $location);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['error'],
            ], 400);
        }

        $host = $validation['host'];

        return response()->json([
            'success' => true,
            'data' => [
                'host' => [
                    'id' => $host->id,
                    'name' => $host->name,
                    'location_name' => $host->location_name,
                    'city' => $host->city,
                    'province' => $host->province,
                    'latitude' => $host->latitude,
                    'longitude' => $host->longitude,
                    'radius_meters' => $host->radius_meters,
                ],
                'program' => [
                    'id' => $host->program->id,
                    'title' => $host->program->title,
                    'program_code' => $host->program->program_code,
                ],
                'validation' => [
                    'is_valid' => true,
                    'is_within_radius' => $validation['within_radius'] ?? false,
                    'distance_meters' => $validation['distance'] ?? null,
                ],
            ],
        ]);
    }

    /**
     * Get learner's programs with schedules.
     */
    public function getProgramsWithSchedules(Request $request): JsonResponse
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $programs = Program::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with(['schedules' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        $programsData = [];

        foreach ($programs as $program) {
            $schedules = $program->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'break_duration' => $schedule->break_duration,
                ];
            });

            $programsData[] = [
                'id' => $program->id,
                'title' => $program->title,
                'program_code' => $program->program_code,
                'description' => $program->description,
                'daily_rate' => $program->daily_rate,
                'start_date' => $program->start_date->format('Y-m-d'),
                'end_date' => $program->end_date->format('Y-m-d'),
                'duration_weeks' => $program->duration_weeks,
                'schedules' => $schedules,
                'has_schedule_today' => $schedules->where('day_of_week', now()->dayOfWeek)->isNotEmpty(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $programsData,
        ]);
    }

    /**
     * Check if current time is within schedule time.
     */
    private function isWithinScheduleTime(string $currentTime, string $startTime, string $endTime): bool
    {
        $current = Carbon::createFromFormat('H:i:s', $currentTime);
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        return $current->between($start, $end);
    }

    /**
     * Get time until schedule starts.
     */
    private function getTimeUntilStart(string $currentTime, string $startTime): ?string
    {
        $current = Carbon::createFromFormat('H:i:s', $currentTime);
        $start = Carbon::createFromFormat('H:i:s', $startTime);

        if ($current->lt($start)) {
            $diff = $current->diff($start);
            return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
        }

        return null;
    }

    /**
     * Get time until schedule ends.
     */
    private function getTimeUntilEnd(string $currentTime, string $endTime): ?string
    {
        $current = Carbon::createFromFormat('H:i:s', $currentTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        if ($current->lt($end)) {
            $diff = $current->diff($end);
            return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
        }

        return null;
    }

    /**
     * Get user's assigned programs and schedules.
     */
    public function getUserPrograms(Request $request): JsonResponse
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // Get user's program enrollments with program details
        $programLearners = ProgramLearner::with(['program.schedules' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('user_id', $user->id)
            ->whereIn('status', ['enrolled', 'active'])
            ->get();

        $programsData = [];

        foreach ($programLearners as $programLearner) {
            $program = $programLearner->program;

            // Get today's schedule for this program
            $todaySchedule = $program->schedules
                ->where('day_of_week', now()->dayOfWeek)
                ->first();

            $programData = [
                'enrollment' => [
                    'id' => $programLearner->id,
                    'enrollment_date' => $programLearner->enrollment_date->format('Y-m-d'),
                    'completion_date' => $programLearner->completion_date?->format('Y-m-d'),
                    'status' => $programLearner->status,
                    'eti_eligible' => $programLearner->eti_eligible,
                    'eti_monthly_amount' => $programLearner->eti_monthly_amount,
                    'attendance_percentage' => $programLearner->attendance_percentage,
                    'notes' => $programLearner->notes,
                    'days_remaining' => $programLearner->getDaysRemaining(),
                ],
                'program' => [
                    'id' => $program->id,
                    'title' => $program->title,
                    'program_code' => $program->program_code,
                    'description' => $program->description,
                    'daily_rate' => $program->daily_rate,
                    'start_date' => $program->start_date->format('Y-m-d'),
                    'end_date' => $program->end_date->format('Y-m-d'),
                    'duration_weeks' => $program->duration_weeks,
                    'total_training_days' => $program->total_training_days,
                    'status' => $program->status,
                    'is_approved' => $program->is_approved,
                ],
                'today_schedule' => null,
                'all_schedules' => [],
            ];

            // Add today's schedule if it exists
            if ($todaySchedule) {
                $programData['today_schedule'] = [
                    'id' => $todaySchedule->id,
                    'day_of_week' => $todaySchedule->day_of_week,
                    'day_name' => $todaySchedule->day_name,
                    'start_time' => $todaySchedule->start_time,
                    'end_time' => $todaySchedule->end_time,
                    'break_duration' => $todaySchedule->break_duration,
                    'is_active' => $todaySchedule->is_active,
                    'is_current_time' => $this->isWithinScheduleTime($currentTime, $todaySchedule->start_time, $todaySchedule->end_time),
                    'time_until_start' => $this->getTimeUntilStart($currentTime, $todaySchedule->start_time),
                    'time_until_end' => $this->getTimeUntilEnd($currentTime, $todaySchedule->end_time),
                ];
            }

            // Add all schedules for the program
            $programData['all_schedules'] = $program->schedules->map(function ($schedule) use ($currentTime) {
                return [
                    'id' => $schedule->id,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'break_duration' => $schedule->break_duration,
                    'is_active' => $schedule->is_active,
                    'is_today' => $schedule->day_of_week === now()->dayOfWeek,
                    'is_current_time' => $this->isWithinScheduleTime($currentTime, $schedule->start_time, $schedule->end_time),
                ];
            })->toArray();

            $programsData[] = $programData;
        }

        // Get attendance summary for the user
        $attendanceSummary = $this->getUserAttendanceSummary($user, $today);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'employee_number' => $user->employee_number,
                ],
                'programs' => $programsData,
                'total_programs' => count($programsData),
                'has_schedule_today' => collect($programsData)->contains('today_schedule', '!=', null),
                'attendance_summary' => $attendanceSummary,
                'current_date' => $today,
                'current_time' => $currentTime,
            ],
        ]);
    }

    /**
     * Get user's primary program (most recent active enrollment).
     */
    public function getPrimaryProgram(Request $request): JsonResponse
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // Get the most recent active program enrollment
        $primaryProgramLearner = ProgramLearner::with(['program.schedules' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('enrollment_date', 'desc')
            ->first();

        if (!$primaryProgramLearner) {
            return response()->json([
                'success' => false,
                'message' => 'No active program found for this user',
            ], 404);
        }

        $program = $primaryProgramLearner->program;

        // Get today's schedule
        $todaySchedule = $program->schedules
            ->where('day_of_week', now()->dayOfWeek)
            ->first();

        $programData = [
            'enrollment' => [
                'id' => $primaryProgramLearner->id,
                'enrollment_date' => $primaryProgramLearner->enrollment_date->format('Y-m-d'),
                'completion_date' => $primaryProgramLearner->completion_date?->format('Y-m-d'),
                'status' => $primaryProgramLearner->status,
                'eti_eligible' => $primaryProgramLearner->eti_eligible,
                'eti_monthly_amount' => $primaryProgramLearner->eti_monthly_amount,
                'attendance_percentage' => $primaryProgramLearner->attendance_percentage,
                'days_remaining' => $primaryProgramLearner->getDaysRemaining(),
            ],
            'program' => [
                'id' => $program->id,
                'title' => $program->title,
                'program_code' => $program->program_code,
                'description' => $program->description,
                'daily_rate' => $program->daily_rate,
                'start_date' => $program->start_date->format('Y-m-d'),
                'end_date' => $program->end_date->format('Y-m-d'),
                'duration_weeks' => $program->duration_weeks,
                'total_training_days' => $program->total_training_days,
            ],
            'today_schedule' => null,
            'has_schedule_today' => false,
        ];

        if ($todaySchedule) {
            $programData['today_schedule'] = [
                'id' => $todaySchedule->id,
                'day_of_week' => $todaySchedule->day_of_week,
                'day_name' => $todaySchedule->day_name,
                'start_time' => $todaySchedule->start_time,
                'end_time' => $todaySchedule->end_time,
                'break_duration' => $todaySchedule->break_duration,
                'is_current_time' => $this->isWithinScheduleTime($currentTime, $todaySchedule->start_time, $todaySchedule->end_time),
                'time_until_start' => $this->getTimeUntilStart($currentTime, $todaySchedule->start_time),
                'time_until_end' => $this->getTimeUntilEnd($currentTime, $todaySchedule->end_time),
            ];
            $programData['has_schedule_today'] = true;
        }

        return response()->json([
            'success' => true,
            'data' => $programData,
        ]);
    }

    /**
     * Get user's attendance summary for today.
     */
    private function getUserAttendanceSummary(User $user, string $date): array
    {
        $todayAttendance = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        if (!$todayAttendance) {
            return [
                'has_attendance_today' => false,
                'status' => null,
                'check_in_time' => null,
                'check_out_time' => null,
                'hours_worked' => 0,
            ];
        }

        return [
            'has_attendance_today' => true,
            'status' => $todayAttendance->status,
            'check_in_time' => $todayAttendance->check_in_time,
            'check_out_time' => $todayAttendance->check_out_time,
            'hours_worked' => $todayAttendance->hours_worked,
            'is_payable' => $todayAttendance->is_payable,
            'calculated_pay' => $todayAttendance->calculated_pay,
        ];
    }
}
