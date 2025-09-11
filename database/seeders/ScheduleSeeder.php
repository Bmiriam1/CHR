<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Program;
use App\Models\User;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company for seeding
        $company = Company::first();
        if (!$company) {
            $this->command->warn('No companies found. Please run CompanySeeder first.');
            return;
        }

        // Get programs for this company
        $programs = Program::where('company_id', $company->id)->get();
        if ($programs->isEmpty()) {
            // Check if there are any programs at all
            $anyPrograms = Program::take(5)->get();
            if ($anyPrograms->isEmpty()) {
                $this->command->warn('No programs found in the system. Creating basic demo schedules without programs.');
                // Create some basic schedules without programs for demonstration
                $this->createDemoSchedules($company);
                return;
            }
            // Use any available programs for demonstration
            $programs = $anyPrograms;
            $this->command->info('Using existing programs from other companies for demonstration.');
        }

        // Get instructors (users with instructor role)
        $instructors = User::where('company_id', $company->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['instructor', 'hr_manager', 'company_admin']);
            })
            ->get();

        if ($instructors->isEmpty()) {
            $instructors = User::where('company_id', $company->id)->limit(3)->get();
        }

        $this->command->info('Creating sample schedules...');

        // Sample schedule data
        $scheduleTemplates = [
            [
                'title' => 'Introduction to Microsoft Word',
                'description' => 'Learn the basics of Microsoft Word including formatting, styles, and document creation.',
                'module_name' => 'Word Processing Fundamentals',
                'unit_standard' => 'US116932',
                'session_type' => 'theory',
                'is_online' => false,
                'venue_name' => 'Training Room A',
                'venue_address' => '123 Training Street, Cape Town',
                'room_number' => 'A101',
                'building' => 'Main Building',
                'campus' => 'Central Campus',
                'expected_attendees' => 20,
                'learning_outcomes' => [
                    'Create and format basic documents',
                    'Apply styles and formatting',
                    'Insert images and tables'
                ],
                'assessment_criteria' => [
                    'Document formatting accuracy',
                    'Proper use of styles',
                    'Professional document presentation'
                ],
            ],
            [
                'title' => 'Excel Spreadsheet Basics',
                'description' => 'Introduction to Microsoft Excel, formulas, and basic data analysis.',
                'module_name' => 'Spreadsheet Applications',
                'unit_standard' => 'US116933',
                'session_type' => 'practical',
                'is_online' => true,
                'platform' => 'Microsoft Teams',
                'meeting_url' => 'https://teams.microsoft.com/l/meetup-join/sample',
                'meeting_id' => '123-456-789',
                'meeting_password' => 'Excel2024',
                'expected_attendees' => 15,
                'learning_outcomes' => [
                    'Create basic spreadsheets',
                    'Use formulas and functions',
                    'Format data effectively'
                ],
                'assessment_criteria' => [
                    'Formula accuracy',
                    'Data organization',
                    'Chart creation'
                ],
            ],
            [
                'title' => 'PowerPoint Presentation Skills',
                'description' => 'Create engaging presentations using Microsoft PowerPoint.',
                'module_name' => 'Presentation Software',
                'unit_standard' => 'US116934',
                'session_type' => 'workshop',
                'is_online' => false,
                'venue_name' => 'Training Room B',
                'venue_address' => '123 Training Street, Cape Town',
                'room_number' => 'B202',
                'building' => 'Main Building',
                'campus' => 'Central Campus',
                'expected_attendees' => 18,
                'learning_outcomes' => [
                    'Design effective slide layouts',
                    'Use animations and transitions',
                    'Deliver presentations confidently'
                ],
                'assessment_criteria' => [
                    'Slide design quality',
                    'Content organization',
                    'Presentation delivery'
                ],
            ],
            [
                'title' => 'Email and Internet Basics',
                'description' => 'Learn email management and safe internet browsing practices.',
                'module_name' => 'Digital Communication',
                'unit_standard' => 'US116935',
                'session_type' => 'theory',
                'is_online' => true,
                'platform' => 'Zoom',
                'meeting_url' => 'https://zoom.us/j/1234567890',
                'meeting_id' => '123-456-7890',
                'meeting_password' => 'Digital2024',
                'expected_attendees' => 25,
                'learning_outcomes' => [
                    'Manage email effectively',
                    'Browse the internet safely',
                    'Understand digital security'
                ],
                'assessment_criteria' => [
                    'Email organization skills',
                    'Security awareness',
                    'Safe browsing practices'
                ],
            ],
            [
                'title' => 'Computer Hardware Basics',
                'description' => 'Understanding computer components and basic troubleshooting.',
                'module_name' => 'Hardware Fundamentals',
                'unit_standard' => 'US116936',
                'session_type' => 'practical',
                'is_online' => false,
                'venue_name' => 'Lab Room C',
                'venue_address' => '123 Training Street, Cape Town',
                'room_number' => 'C301',
                'building' => 'Technical Building',
                'campus' => 'Central Campus',
                'expected_attendees' => 12,
                'learning_outcomes' => [
                    'Identify computer components',
                    'Perform basic maintenance',
                    'Troubleshoot common issues'
                ],
                'assessment_criteria' => [
                    'Component identification',
                    'Troubleshooting accuracy',
                    'Maintenance procedures'
                ],
            ],
        ];

        // Create schedules for the current month and next month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();

        $scheduleCount = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate) && $scheduleCount < 50) {
            // Skip weekends for most sessions
            if ($currentDate->isWeekend() && rand(1, 10) > 2) {
                $currentDate->addDay();
                continue;
            }

            // Create 1-3 sessions per day randomly
            $sessionsPerDay = rand(0, 3);

            for ($i = 0; $i < $sessionsPerDay; $i++) {
                $template = $scheduleTemplates[array_rand($scheduleTemplates)];
                $program = $programs->random();
                $instructor = $instructors->random();

                // Generate random times
                $startHour = rand(8, 15); // 8 AM to 3 PM
                $startTime = sprintf('%02d:%02d:00', $startHour, rand(0, 3) * 15); // 15-minute intervals
                $endTime = sprintf('%02d:%02d:00', $startHour + rand(1, 3), rand(0, 3) * 15);

                // Determine status based on date
                $status = 'scheduled';
                $actualAttendees = null;
                $attendanceRate = null;

                if ($currentDate->isPast()) {
                    $status = rand(1, 10) > 8 ? 'cancelled' : 'completed';
                    if ($status === 'completed') {
                        $actualAttendees = rand(
                            (int)($template['expected_attendees'] * 0.6),
                            $template['expected_attendees']
                        );
                        $attendanceRate = ($actualAttendees / $template['expected_attendees']) * 100;
                    }
                } elseif ($currentDate->isToday()) {
                    $status = rand(1, 5) > 3 ? 'in_progress' : 'scheduled';
                }

                $schedule = Schedule::create([
                    'company_id' => $company->id,
                    'program_id' => $program->id,
                    'instructor_id' => $instructor->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'session_date' => $currentDate->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'module_name' => $template['module_name'],
                    'unit_standard' => $template['unit_standard'],
                    'session_type' => $template['session_type'],
                    'is_online' => $template['is_online'],
                    'venue_name' => $template['venue_name'] ?? null,
                    'venue_address' => $template['venue_address'] ?? null,
                    'room_number' => $template['room_number'] ?? null,
                    'building' => $template['building'] ?? null,
                    'campus' => $template['campus'] ?? null,
                    'platform' => $template['platform'] ?? null,
                    'meeting_url' => $template['meeting_url'] ?? null,
                    'meeting_id' => $template['meeting_id'] ?? null,
                    'meeting_password' => $template['meeting_password'] ?? null,
                    'expected_attendees' => $template['expected_attendees'],
                    'actual_attendees' => $actualAttendees,
                    'attendance_rate' => $attendanceRate,
                    'status' => $status,
                    'qr_code_active' => true,
                    'learning_outcomes' => $template['learning_outcomes'],
                    'assessment_criteria' => $template['assessment_criteria'],
                    'send_reminders' => true,
                    'created_by' => $instructor->id,
                ]);

                $scheduleCount++;

                if ($scheduleCount >= 50) break;
            }

            $currentDate->addDay();
        }

        $this->command->info("Created {$scheduleCount} sample schedules successfully!");

        // Show summary
        $this->command->table(
            ['Status', 'Count'],
            [
                ['Scheduled', Schedule::where('company_id', $company->id)->where('status', 'scheduled')->count()],
                ['In Progress', Schedule::where('company_id', $company->id)->where('status', 'in_progress')->count()],
                ['Completed', Schedule::where('company_id', $company->id)->where('status', 'completed')->count()],
                ['Cancelled', Schedule::where('company_id', $company->id)->where('status', 'cancelled')->count()],
                ['Online Sessions', Schedule::where('company_id', $company->id)->where('is_online', true)->count()],
                ['In-Person Sessions', Schedule::where('company_id', $company->id)->where('is_online', false)->count()],
            ]
        );
    }

    /**
     * Create demo schedules without programs for testing.
     */
    private function createDemoSchedules($company): void
    {
        $this->command->info('Creating demo schedules...');

        $instructors = User::where('company_id', $company->id)->limit(3)->get();
        if ($instructors->isEmpty()) {
            $instructors = User::limit(3)->get();
        }

        if ($instructors->isEmpty()) {
            $this->command->error('No users found to assign as instructors.');
            return;
        }

        $scheduleCount = 0;
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate) && $scheduleCount < 20) {
            // Skip weekends for most sessions
            if ($currentDate->isWeekend() && rand(1, 10) > 3) {
                $currentDate->addDay();
                continue;
            }

            // Create 1-2 sessions per day randomly
            $sessionsPerDay = rand(0, 2);

            for ($i = 0; $i < $sessionsPerDay; $i++) {
                $instructor = $instructors->random();

                // Generate random times
                $startHour = rand(8, 15);
                $startTime = sprintf('%02d:%02d:00', $startHour, rand(0, 3) * 15);
                $endTime = sprintf('%02d:%02d:00', $startHour + rand(1, 3), rand(0, 3) * 15);

                // Demo session titles
                $titles = [
                    'Team Meeting',
                    'Training Workshop',
                    'Skills Development Session',
                    'Professional Development',
                    'Team Building Activity',
                    'Knowledge Sharing Session',
                    'Demo Presentation',
                    'Review Meeting'
                ];

                $status = 'scheduled';
                $actualAttendees = null;
                $attendanceRate = null;

                if ($currentDate->isPast()) {
                    $status = rand(1, 10) > 8 ? 'cancelled' : 'completed';
                    if ($status === 'completed') {
                        $expectedAttendees = rand(10, 25);
                        $actualAttendees = rand((int)($expectedAttendees * 0.6), $expectedAttendees);
                        $attendanceRate = ($actualAttendees / $expectedAttendees) * 100;
                    }
                } elseif ($currentDate->isToday()) {
                    $status = rand(1, 5) > 3 ? 'in_progress' : 'scheduled';
                }

                $expectedAttendees = $actualAttendees ?? rand(10, 25);

                Schedule::create([
                    'company_id' => $company->id,
                    'program_id' => null, // No program for demo
                    'instructor_id' => $instructor->id,
                    'title' => $titles[array_rand($titles)],
                    'description' => 'Demo schedule for testing purposes',
                    'session_date' => $currentDate->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'session_type' => ['theory', 'practical', 'workshop'][rand(0, 2)],
                    'is_online' => rand(0, 1) === 1,
                    'venue_name' => rand(0, 1) === 1 ? 'Training Room ' . chr(65 + rand(0, 2)) : null,
                    'expected_attendees' => $expectedAttendees,
                    'actual_attendees' => $actualAttendees,
                    'attendance_rate' => $attendanceRate,
                    'status' => $status,
                    'qr_code_active' => true,
                    'send_reminders' => true,
                    'created_by' => $instructor->id,
                ]);

                $scheduleCount++;

                if ($scheduleCount >= 20) break;
            }

            $currentDate->addDay();
        }

        $this->command->info("Created {$scheduleCount} demo schedules successfully!");
    }
}
