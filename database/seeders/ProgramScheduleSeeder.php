<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProgramScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Creating schedules for programs...');
        }

        // Get all active programs
        $programs = Program::where('status', 'active')->get();

        if ($programs->isEmpty()) {
            if ($this->command) {
                $this->command->warn('No active programs found. Creating a sample program first...');
            }

            // Create a sample program if none exists
            $program = Program::create([
                'title' => 'Digital Skills Development Program',
                'program_code' => 'DSDP-2025',
                'description' => 'Comprehensive digital skills training program',
                'company_id' => 1,
                'program_type_id' => 1,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'daily_rate' => 350.00,
                'max_learners' => 25,
                'status' => 'active',
                'is_approved' => true,
                'location_type' => 'hybrid',
                'venue' => 'Main Training Center',
                'venue_address' => '123 Training Street, Cape Town',
            ]);

            $programs = collect([$program]);
        }

        foreach ($programs as $program) {
            $this->createSchedulesForProgram($program);
        }

        if ($this->command) {
            $this->command->info('Schedule creation completed!');
        }
    }

    /**
     * Create schedules for a specific program.
     */
    private function createSchedulesForProgram(Program $program): void
    {
        if ($this->command) {
            $this->command->info("Creating schedules for program: {$program->title}");
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

        // Define schedule patterns based on program type
        $schedulePatterns = $this->getSchedulePatterns($program);

        foreach ($schedulePatterns as $pattern) {
            $this->createScheduleFromPattern($program, $pattern, $startDate, $endDate);
        }

        if ($this->command) {
            $this->command->info("Created schedules for program: {$program->title}");
        }
    }

    /**
     * Get schedule patterns based on program type.
     */
    private function getSchedulePatterns(Program $program): array
    {
        $patterns = [];

        // Morning session (8:00 AM - 12:00 PM)
        $patterns[] = [
            'title' => 'Morning Training Session',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'break_start_time' => '10:00:00',
            'break_end_time' => '10:15:00',
            'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'session_type' => 'training',
            'is_online' => $program->location_type === 'online',
        ];

        // Afternoon session (1:00 PM - 5:00 PM)
        $patterns[] = [
            'title' => 'Afternoon Training Session',
            'start_time' => '13:00:00',
            'end_time' => '17:00:00',
            'break_start_time' => '15:00:00',
            'break_end_time' => '15:15:00',
            'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'session_type' => 'training',
            'is_online' => $program->location_type === 'online',
        ];

        // Weekend session (Saturday 9:00 AM - 1:00 PM)
        if ($program->location_type !== 'online') {
            $patterns[] = [
                'title' => 'Weekend Practical Session',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'break_start_time' => '11:00:00',
                'break_end_time' => '11:15:00',
                'days' => ['saturday'],
                'session_type' => 'practical',
                'is_online' => false,
            ];
        }

        return $patterns;
    }

    /**
     * Create schedule from pattern.
     */
    private function createScheduleFromPattern(Program $program, array $pattern, Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = strtolower($currentDate->format('l'));

            if (in_array($dayOfWeek, $pattern['days'])) {
                $this->createSchedule($program, $pattern, $currentDate);
            }

            $currentDate->addDay();
        }
    }

    /**
     * Create a single schedule.
     */
    private function createSchedule(Program $program, array $pattern, Carbon $date): void
    {
        // Calculate duration
        $startTime = Carbon::createFromTimeString($pattern['start_time']);
        $endTime = Carbon::createFromTimeString($pattern['end_time']);
        $breakStart = Carbon::createFromTimeString($pattern['break_start_time']);
        $breakEnd = Carbon::createFromTimeString($pattern['break_end_time']);

        // Ensure end time is after start time
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        $plannedDuration = $startTime->diffInMinutes($endTime) / 60;
        $breakDuration = $breakStart->diffInMinutes($breakEnd) / 60;
        $netTrainingHours = $plannedDuration - $breakDuration;

        // Get a random instructor (or create one if none exists)
        $instructor = User::where('company_id', $program->company_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'instructor');
            })
            ->inRandomOrder()
            ->first();

        if (!$instructor) {
            $instructor = User::where('company_id', $program->company_id)->first();
        }

        // Generate session code
        $sessionCode = $program->program_code . '-' . $date->format('Ymd') . '-' . substr($pattern['start_time'], 0, 2);

        // Create the schedule
        Schedule::create([
            'company_id' => $program->company_id,
            'program_id' => $program->id,
            'instructor_id' => $instructor?->id,

            'title' => $pattern['title'],
            'description' => "Training session for {$program->title}",
            'session_code' => $sessionCode,
            'session_date' => $date->format('Y-m-d'),
            'start_time' => $pattern['start_time'],
            'end_time' => $pattern['end_time'],
            'break_start_time' => $pattern['break_start_time'],
            'break_end_time' => $pattern['break_end_time'],
            'planned_duration_hours' => $plannedDuration,
            'break_duration_hours' => $breakDuration,
            'net_training_hours' => $netTrainingHours,

            // Recurrence
            'recurrence_type' => 'none',
            'monday' => in_array('monday', $pattern['days']),
            'tuesday' => in_array('tuesday', $pattern['days']),
            'wednesday' => in_array('wednesday', $pattern['days']),
            'thursday' => in_array('thursday', $pattern['days']),
            'friday' => in_array('friday', $pattern['days']),
            'saturday' => in_array('saturday', $pattern['days']),
            'sunday' => in_array('sunday', $pattern['days']),

            // Location
            'venue_name' => $program->venue,
            'venue_address' => $program->venue_address,
            'is_online' => $pattern['is_online'],
            'meeting_url' => $pattern['is_online'] ? 'https://meet.google.com/' . \Illuminate\Support\Str::random(10) : null,
            'platform' => $pattern['is_online'] ? 'Google Meet' : null,

            // Curriculum
            'module_name' => $this->getModuleName($pattern['session_type']),
            'learning_outcomes' => json_encode($this->getLearningOutcomes($pattern['session_type'])),
            'required_materials' => json_encode($this->getRequiredMaterials($pattern['session_type'])),

            // Attendance
            'expected_attendees' => $program->participants()->count(),
            'check_in_opens_at' => $date->copy()->setTimeFromTimeString($pattern['start_time'])->subMinutes(30),
            'check_in_closes_at' => $date->copy()->setTimeFromTimeString($pattern['start_time'])->addMinutes(15),
            'allow_late_check_in' => true,
            'late_threshold_minutes' => 15,

            // Status
            'status' => $date->isPast() ? 'completed' : 'scheduled',
            'session_type' => 'training', // Use valid enum value
            'send_reminders' => true,
            'created_by' => $instructor?->id ?? 1,
        ]);
    }

    /**
     * Get module name based on session type.
     */
    private function getModuleName(string $sessionType): string
    {
        return match ($sessionType) {
            'training' => 'Core Skills Development',
            'practical' => 'Hands-on Practice',
            'assessment' => 'Skills Assessment',
            default => 'General Training',
        };
    }

    /**
     * Get learning outcomes based on session type.
     */
    private function getLearningOutcomes(string $sessionType): array
    {
        return match ($sessionType) {
            'training' => [
                'Understand core concepts',
                'Apply theoretical knowledge',
                'Develop practical skills',
            ],
            'practical' => [
                'Hands-on experience',
                'Real-world application',
                'Problem-solving skills',
            ],
            'assessment' => [
                'Evaluate understanding',
                'Measure progress',
                'Identify areas for improvement',
            ],
            default => [
                'General skill development',
            ],
        };
    }

    /**
     * Get required materials based on session type.
     */
    private function getRequiredMaterials(string $sessionType): array
    {
        return match ($sessionType) {
            'training' => [
                'Laptop/Computer',
                'Notebook',
                'Pen',
                'Training materials',
            ],
            'practical' => [
                'Laptop/Computer',
                'Project materials',
                'Tools and equipment',
                'Safety equipment',
            ],
            'assessment' => [
                'Laptop/Computer',
                'Assessment materials',
                'Calculator',
                'Reference materials',
            ],
            default => [
                'Basic materials',
            ],
        };
    }
}
