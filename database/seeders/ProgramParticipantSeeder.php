<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\User;
use App\Models\ProgramParticipant;

class ProgramParticipantSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all programs
        $programs = Program::withoutGlobalScopes()->get();

        if ($programs->isEmpty()) {
            $this->command->warn('No programs found. Please run ProgramSeeder first.');
            return;
        }

        // Get learners (users with learner role)
        $learners = User::whereHas('roles', function ($q) {
            $q->where('name', 'learner');
        })->get();

        if ($learners->isEmpty()) {
            $this->command->warn('No learners found. Please run LearnerIRP5Seeder first.');
            return;
        }

        $this->command->info('Assigning participants to programs...');

        foreach ($programs as $program) {
            // Assign 3-8 random learners to each program
            $participantCount = rand(3, min(8, $learners->count()));
            $selectedLearners = $learners->random($participantCount);

            foreach ($selectedLearners as $learner) {
                // Check if participant already exists
                $existingParticipant = ProgramParticipant::where('program_id', $program->id)
                    ->where('user_id', $learner->id)
                    ->first();

                if (!$existingParticipant) {
                    ProgramParticipant::create([
                        'program_id' => $program->id,
                        'user_id' => $learner->id,
                        'company_id' => $program->company_id,
                        'status' => ['enrolled', 'active', 'absconded', 'completed'][rand(0, 2)],
                        'enrolled_at' => now()->subDays(rand(1, 30)),
                        'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                        'notes' => rand(0, 1) ? 'Enrolled via seeder' : null,
                        'enrolled_by' => User::whereHas('roles', function ($q) {
                            $q->whereIn('name', ['hr_manager', 'company_admin']);
                        })->first()?->id,
                    ]);
                }
            }

            $this->command->info("Assigned participants to program: {$program->title}");
        }

        $this->command->info('Program participants seeded successfully!');
    }
}
