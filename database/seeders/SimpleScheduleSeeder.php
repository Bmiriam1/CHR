<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SimpleScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Creating simple schedules for programs...');
        }

        // Get all active programs
        $programs = Program::where('status', 'active')->get();

        if ($programs->isEmpty()) {
            if ($this->command) {
                $this->command->warn('No active programs found.');
            }
            return;
        }

        foreach ($programs as $program) {
            $this->createSimpleSchedulesForProgram($program);
        }

        if ($this->command) {
            $this->command->info('Simple schedule creation completed!');
        }
    }

    /**
     * Create simple schedules for a specific program.
     */
    private function createSimpleSchedulesForProgram(Program $program): void
    {
        if ($this->command) {
            $this->command->info("Creating simple schedules for program: {$program->title}");
        }

        // Get program participants
        $participants = $program->participants()->with('user')->get();

        if ($participants->isEmpty()) {
            if ($this->command) {
                $this->command->warn("No participants found for program: {$program->title}");
            }
            return;
        }

        // Create schedules for the next 30 days
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(30)->endOfDay();

        // Create morning and afternoon sessions for weekdays
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Only create schedules for weekdays
            if ($currentDate->isWeekday()) {
                // Morning session (8:00 AM - 12:00 PM)
                $this->createSimpleSchedule($program, $currentDate, '08:00:00', '12:00:00', 'Morning Training Session');

                // Afternoon session (1:00 PM - 5:00 PM)
                $this->createSimpleSchedule($program, $currentDate, '13:00:00', '17:00:00', 'Afternoon Training Session');
            }

            $currentDate->addDay();
        }

        if ($this->command) {
            $this->command->info("Created simple schedules for program: {$program->title}");
        }
    }

    /**
     * Create a simple schedule.
     */
    private function createSimpleSchedule(Program $program, Carbon $date, string $startTime, string $endTime, string $title): void
    {
        // Get a random instructor
        $instructor = User::where('company_id', $program->company_id)->first();

        // Generate session code
        $sessionCode = $program->program_code . '-' . $date->format('Ymd') . '-' . substr($startTime, 0, 2);

        // Calculate duration
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        // Ensure end time is after start time
        if ($end->lt($start)) {
            $end->addDay();
        }

        $plannedDuration = $start->diffInMinutes($end) / 60;

        // Create the schedule
        Schedule::create([
            'company_id' => $program->company_id,
            'program_id' => $program->id,
            'instructor_id' => $instructor?->id,

            'title' => $title,
            'description' => "Training session for {$program->title}",
            'session_code' => $sessionCode,
            'session_date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'break_start_time' => null,
            'break_end_time' => null,
            'planned_duration_hours' => $plannedDuration,
            'break_duration_hours' => 0,
            'net_training_hours' => $plannedDuration,

            // Recurrence
            'recurrence_type' => 'none',
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,

            // Location
            'venue_name' => $program->venue ?? 'Training Center',
            'venue_address' => $program->venue_address ?? '123 Training Street',
            'is_online' => $program->location_type === 'online',
            'meeting_url' => $program->location_type === 'online' ? 'https://meet.google.com/abc123' : null,
            'platform' => $program->location_type === 'online' ? 'Google Meet' : null,

            // Curriculum
            'module_name' => 'Core Skills Development',
            'learning_outcomes' => json_encode(['Understand core concepts', 'Apply knowledge']),
            'required_materials' => json_encode(['Laptop', 'Notebook', 'Pen']),

            // Attendance
            'expected_attendees' => $program->participants()->count(),
            'check_in_opens_at' => $date->copy()->setTimeFromTimeString($startTime)->subMinutes(30),
            'check_in_closes_at' => $date->copy()->setTimeFromTimeString($startTime)->addMinutes(15),
            'allow_late_check_in' => true,
            'late_threshold_minutes' => 15,

            // Status
            'status' => $date->isPast() ? 'completed' : 'scheduled',
            'session_type' => 'lecture',
            'send_reminders' => true,
            'created_by' => $instructor?->id ?? 1,
        ]);
    }
}
