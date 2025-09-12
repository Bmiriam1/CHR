<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Program;
use App\Models\User;
use App\Models\ProgramParticipant;
use Carbon\Carbon;

class ProgramParticipantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first program
        $program = Program::first();
        if (!$program) {
            $this->command->info('No programs found. Please run ProgramSeeder first.');
            return;
        }

        // Get users from the same company
        $users = User::where('company_id', $program->company_id)
            ->where('is_learner', true)
            ->limit(10)
            ->get();

        if ($users->isEmpty()) {
            $this->command->info('No learner users found. Creating some...');

            // Create some learner users
            for ($i = 1; $i <= 10; $i++) {
                $user = User::create([
                    'first_name' => "Learner",
                    'last_name' => "{$i}",
                    'email' => "learner{$i}@example.com",
                    'password' => bcrypt('password'),
                    'company_id' => $program->company_id,
                    'is_learner' => true,
                    'email_verified_at' => now(),
                ]);

                // Assign learner role
                $user->assignRole('learner');

                $users->push($user);
            }
        }

        $this->command->info("Adding {$users->count()} participants to program: {$program->name}");

        // Clear existing participants for this program
        ProgramParticipant::where('program_id', $program->id)->delete();

        // Add participants to the program
        foreach ($users as $user) {
            ProgramParticipant::create([
                'company_id' => $program->company_id,
                'program_id' => $program->id,
                'user_id' => $user->id,
                'enrolled_at' => now()->subDays(rand(1, 30)),
                'status' => 'active',
                'notes' => 'Enrolled via seeder',
            ]);
        }

        $this->command->info('Program participants seeded successfully!');
    }
}
