<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramBudget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProgramBudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Creating program budgets...');
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
            $this->createBudgetForProgram($program);
        }

        if ($this->command) {
            $this->command->info('Program budgets creation completed!');
        }
    }

    /**
     * Create budget for a specific program.
     */
    private function createBudgetForProgram(Program $program): void
    {
        if ($this->command) {
            $this->command->info("Creating budget for program: {$program->title}");
        }

        // Get program participants count
        $participantCount = $program->participants()->count();

        if ($participantCount === 0) {
            if ($this->command) {
                $this->command->warn("No participants found for program: {$program->title}");
            }
            return;
        }

        // Calculate budget period (next 3 months)
        $budgetStartDate = now()->startOfMonth();
        $budgetEndDate = now()->addMonths(3)->endOfMonth();

        // Calculate total budget based on participants and program duration
        $totalDays = $budgetStartDate->diffInDays($budgetEndDate);
        $workingDays = $this->calculateWorkingDays($budgetStartDate, $budgetEndDate);
        $baseDailyRate = $program->daily_rate ?? 350.00;

        // Calculate different daily rates
        $rates = $this->calculateDailyRates($baseDailyRate);

        // Calculate total budget
        $totalBudget = $this->calculateTotalBudget($participantCount, $workingDays, $rates);

        // Get a user to assign as creator
        $creator = User::where('company_id', $program->company_id)->first();

        // Create the budget
        ProgramBudget::create([
            'program_id' => $program->id,
            'company_id' => $program->company_id,

            'budget_start_date' => $budgetStartDate,
            'budget_end_date' => $budgetEndDate,
            'budget_name' => "Budget for {$program->title} - Q" . now()->quarter,
            'description' => "Quarterly budget for {$program->title} covering {$participantCount} participants",

            // Daily rates
            'travel_daily_rate' => $rates['travel'],
            'online_daily_rate' => $rates['online'],
            'equipment_daily_rate' => $rates['equipment'],
            'onsite_daily_rate' => $rates['onsite'],

            // Allowances
            'travel_allowance' => 50.00,
            'meal_allowance' => 30.00,
            'accommodation_allowance' => 100.00,
            'equipment_allowance' => 25.00,

            // Budget totals
            'total_budget' => $totalBudget,
            'used_budget' => 0,
            'remaining_budget' => $totalBudget,

            // Settings
            'is_active' => true,
            'auto_calculate_rates' => true,
            'rate_calculation_rules' => [
                'travel_multiplier' => 1.1,
                'online_multiplier' => 0.8,
                'equipment_multiplier' => 1.05,
                'onsite_multiplier' => 1.0,
            ],

            // Status
            'status' => 'active',
            'created_by' => $creator?->id ?? 1,
            'approved_by' => $creator?->id ?? 1,
            'approved_at' => now(),
            'approval_notes' => 'Auto-approved for seeding',
        ]);

        if ($this->command) {
            $this->command->info("Created budget for program: {$program->title} - R" . number_format($totalBudget, 2));
        }
    }

    /**
     * Calculate working days between two dates.
     */
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Monday to Friday are working days
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Calculate daily rates for different attendance types.
     */
    private function calculateDailyRates(float $baseRate): array
    {
        return [
            'travel' => $baseRate * 1.1,      // 10% more for travel
            'online' => $baseRate * 0.8,      // 20% less for online
            'equipment' => $baseRate * 1.05,  // 5% more for equipment
            'onsite' => $baseRate,            // Base rate for onsite
        ];
    }

    /**
     * Calculate total budget based on participants and working days.
     */
    private function calculateTotalBudget(int $participantCount, int $workingDays, array $rates): float
    {
        // Assume 70% onsite, 20% online, 8% travel, 2% equipment
        $distribution = [
            'onsite' => 0.70,
            'online' => 0.20,
            'travel' => 0.08,
            'equipment' => 0.02,
        ];

        $totalBudget = 0;

        foreach ($distribution as $type => $percentage) {
            $participantsForType = $participantCount * $percentage;
            $dailyRate = $rates[$type];
            $typeBudget = $participantsForType * $workingDays * $dailyRate;
            $totalBudget += $typeBudget;
        }

        // Add 10% buffer for unexpected costs
        return $totalBudget * 1.1;
    }
}
