<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Company;
use App\Models\User;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            $this->command->error('No company found. Please run company seeder first.');
            return;
        }

        $program = Program::firstOrCreate(
            ['title' => 'Skills Development Program 2024'],
            [
                'description' => 'Comprehensive skills development program for learners',
                'program_code' => 'SDP-2024-001',
                'start_date' => now()->subMonth()->startOfMonth(),
                'end_date' => now()->addMonths(6),
                'daily_rate' => 150.00,
                'company_id' => $company->id,
                'program_type_id' => 1, // Learnership
            ]
        );

        $this->command->info("Created program: {$program->title}");
    }
}
