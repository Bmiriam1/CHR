<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Program;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Creating attendance records for learners...');
        }

        // Get all active programs with participants
        $programs = Program::where('status', 'active')
            ->with(['participants.user', 'schedules'])
            ->get();

        if ($programs->isEmpty()) {
            if ($this->command) {
                $this->command->warn('No active programs found.');
            }
            return;
        }

        foreach ($programs as $program) {
            $this->createAttendanceForProgram($program);
        }

        if ($this->command) {
            $this->command->info('Attendance records creation completed!');
        }
    }

    /**
     * Create attendance records for a specific program.
     */
    private function createAttendanceForProgram(Program $program): void
    {
        if ($this->command) {
            $this->command->info("Creating attendance for program: {$program->title}");
        }

        $participants = $program->participants()->with('user')->get();

        if ($participants->isEmpty()) {
            if ($this->command) {
                $this->command->warn("No participants found for program: {$program->title}");
            }
            return;
        }

        // Get schedules for the last 30 days (bypass tenant scope)
        $schedules = DB::table('schedules')
            ->where('program_id', $program->id)
            ->where('session_date', '>=', now()->subDays(30))
            ->where('session_date', '<=', now()->addDays(30))
            ->orderBy('session_date')
            ->get();

        if ($schedules->isEmpty()) {
            if ($this->command) {
                $this->command->warn("No schedules found for program: {$program->title}");
            }
            return;
        }

        // Group schedules by date to avoid duplicate attendance records
        $schedulesByDate = $schedules->groupBy('session_date');

        foreach ($participants as $participant) {
            $this->createAttendanceForParticipantByDate($participant, $schedulesByDate, $program);
        }

        if ($this->command) {
            $this->command->info("Created attendance for program: {$program->title}");
        }
    }

    /**
     * Create attendance records for a specific participant by date.
     */
    private function createAttendanceForParticipantByDate($participant, $schedulesByDate, Program $program): void
    {
        $user = $participant->user;
        $attendanceRate = $this->getAttendanceRate($user);

        foreach ($schedulesByDate as $date => $schedules) {
            // Pick the first schedule for this date
            $schedule = $schedules->first();
            $this->createAttendanceRecord($user, $schedule, $program, $attendanceRate);
        }
    }

    /**
     * Create attendance records for a specific participant.
     */
    private function createAttendanceForParticipant($participant, $schedules, Program $program): void
    {
        $user = $participant->user;
        $attendanceRate = $this->getAttendanceRate($user);

        foreach ($schedules as $schedule) {
            $this->createAttendanceRecord($user, $schedule, $program, $attendanceRate);
        }
    }

    /**
     * Get attendance rate for a user (simulate different attendance patterns).
     */
    private function getAttendanceRate(User $user): float
    {
        // Simulate different attendance rates based on user characteristics
        $baseRate = 0.85; // 85% base attendance rate

        // Adjust based on user ID (for consistency)
        $variation = ($user->id % 10) / 100; // 0-9% variation

        return min(1.0, max(0.3, $baseRate + $variation));
    }

    /**
     * Create a single attendance record.
     */
    private function createAttendanceRecord(User $user, $schedule, Program $program, float $attendanceRate): void
    {
        // Determine if user attended based on attendance rate
        $attended = (mt_rand(0, 100) / 100) <= $attendanceRate;

        if (!$attended) {
            // Create absent record
            $this->createAbsentRecord($user, $schedule, $program);
            return;
        }

        // Create present record with realistic times
        $this->createPresentRecord($user, $schedule, $program);
    }

    /**
     * Create a present attendance record.
     */
    private function createPresentRecord(User $user, $schedule, Program $program): void
    {
        $sessionDate = Carbon::parse($schedule->session_date);
        $startTime = Carbon::createFromTimeString($schedule->start_time);
        $endTime = Carbon::createFromTimeString($schedule->end_time);

        // Calculate check-in time (slightly before or after start time)
        $checkInTime = $startTime->copy()->addMinutes(mt_rand(-10, 15));

        // Calculate check-out time (slightly before or after end time)
        $checkOutTime = $endTime->copy()->addMinutes(mt_rand(-5, 10));

        // Calculate break times (handle null values)
        $breakStart = $schedule->break_start_time ? Carbon::createFromTimeString($schedule->break_start_time) : null;
        $breakEnd = $schedule->break_end_time ? Carbon::createFromTimeString($schedule->break_end_time) : null;

        // Calculate hours worked
        $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
        $breakMinutes = ($breakStart && $breakEnd) ? $breakStart->diffInMinutes($breakEnd) : 0;
        $workMinutes = $totalMinutes - $breakMinutes;
        $hoursWorked = $workMinutes / 60;

        // Determine status
        $status = $this->determineAttendanceStatus($checkInTime, $startTime);

        // Determine attendance type
        $attendanceType = $this->determineAttendanceType($schedule);

        // Calculate pay
        $dailyRate = $this->getDailyRateForAttendance($program, $attendanceType);
        $calculatedPay = max(0, $dailyRate * ($hoursWorked / 8)); // Pro-rated based on 8-hour day, ensure positive

        AttendanceRecord::create([
            'company_id' => $program->company_id,
            'user_id' => $user->id,
            'program_id' => $program->id,
            'schedule_id' => $schedule->id,

            'attendance_date' => $sessionDate,
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'break_start_time' => $breakStart,
            'break_end_time' => $breakEnd,
            'hours_worked' => $hoursWorked,
            'break_duration' => $breakMinutes / 60,
            'overtime_hours' => max(0, $hoursWorked - 8),

            'status' => $status,
            'attendance_type' => $attendanceType,
            'is_validated' => true,
            'has_anomaly' => false,
            'requires_approval' => false,

            'is_payable' => true,
            'daily_rate_applied' => $dailyRate,
            'calculated_pay' => $calculatedPay,
            'is_partial_day' => $hoursWorked < 7,
            'partial_day_percentage' => $hoursWorked < 7 ? max(0, ($hoursWorked / 8) * 100) : 100,

            'notes' => $this->generateAttendanceNotes($status, $attendanceType),
            'validated_at' => now(),
            'validated_by' => 1, // System validation
        ]);
    }

    /**
     * Create an absent attendance record.
     */
    private function createAbsentRecord(User $user, $schedule, Program $program): void
    {
        $sessionDate = Carbon::parse($schedule->session_date);

        // Determine absence reason
        $absenceReasons = ['sick', 'personal', 'transport_issue', 'family_emergency'];
        $reason = $absenceReasons[array_rand($absenceReasons)];

        // Determine if absence is authorized
        $isAuthorized = mt_rand(0, 100) <= 70; // 70% chance of authorized absence

        AttendanceRecord::create([
            'company_id' => $program->company_id,
            'user_id' => $user->id,
            'program_id' => $program->id,
            'schedule_id' => $schedule->id,

            'attendance_date' => $sessionDate,
            'check_in_time' => null,
            'check_out_time' => null,
            'break_start_time' => null,
            'break_end_time' => null,
            'hours_worked' => 0,
            'break_duration' => 0,
            'overtime_hours' => 0,

            'status' => $isAuthorized ? 'excused' : 'absent_unauthorized',
            'attendance_type' => 'regular',
            'is_validated' => $isAuthorized,
            'has_anomaly' => !$isAuthorized,
            'requires_approval' => !$isAuthorized,

            'is_payable' => $isAuthorized,
            'daily_rate_applied' => 0,
            'calculated_pay' => 0,
            'is_partial_day' => false,
            'partial_day_percentage' => 0,

            'notes' => "Absent due to: {$reason}",
            'exception_reason' => $reason,
            'requires_approval' => !$isAuthorized,
        ]);
    }

    /**
     * Determine attendance status based on check-in time.
     */
    private function determineAttendanceStatus(Carbon $checkInTime, Carbon $startTime): string
    {
        $minutesLate = $checkInTime->diffInMinutes($startTime, false);

        if ($minutesLate <= 0) {
            return 'present';
        } elseif ($minutesLate <= 15) {
            return 'late';
        } else {
            return 'late';
        }
    }

    /**
     * Determine attendance type based on schedule.
     */
    private function determineAttendanceType($schedule): string
    {
        if ($schedule->is_online) {
            return 'regular'; // Use regular for online
        }

        // Randomly assign other types
        $types = ['regular', 'makeup', 'overtime'];
        return $types[array_rand($types)];
    }

    /**
     * Get daily rate for attendance type.
     */
    private function getDailyRateForAttendance(Program $program, string $attendanceType): float
    {
        $baseRate = $program->daily_rate ?? 350.00;

        return match ($attendanceType) {
            'online' => $baseRate * 0.8, // 20% less for online
            'travel' => $baseRate * 1.1, // 10% more for travel
            'equipment' => $baseRate * 1.05, // 5% more for equipment
            default => $baseRate,
        };
    }

    /**
     * Generate attendance notes.
     */
    private function generateAttendanceNotes(string $status, string $attendanceType): string
    {
        $notes = [];

        if ($status === 'late') {
            $notes[] = 'Arrived late to session';
        }

        if ($attendanceType === 'travel') {
            $notes[] = 'Travel-based attendance';
        } elseif ($attendanceType === 'equipment') {
            $notes[] = 'Equipment hire attendance';
        } elseif ($attendanceType === 'online') {
            $notes[] = 'Online attendance';
        }

        return implode('. ', $notes) ?: 'Regular attendance';
    }
}
