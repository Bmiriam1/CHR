<?php

namespace Database\Seeders;

use App\Models\SimCard;
use App\Models\SimCardAllocation;
use App\Models\User;
use App\Models\Program;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimCardAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating SIM card allocations...');

        // Get all companies with programs and learners
        $companies = Company::with(['programs', 'users'])->get();

        foreach ($companies as $company) {
            $this->createAllocationsForCompany($company);
        }

        $this->command->info('SIM card allocation seeding completed.');
    }

    private function createAllocationsForCompany(Company $company): void
    {
        // Get available SIM cards for this company
        $availableSimCards = SimCard::where('company_id', $company->id)
            ->where('status', 'available')
            ->get();

        if ($availableSimCards->isEmpty()) {
            $this->command->info("No available SIM cards found for company: {$company->name}");
            return;
        }

        // Get active programs for this company
        $activePrograms = $company->programs()->where('status', 'active')->get();

        if ($activePrograms->isEmpty()) {
            $this->command->info("No active programs found for company: {$company->name}");
            return;
        }

        // Get learners enrolled in programs for this company
        $learners = User::where('company_id', $company->id)
            ->where('is_learner', true)
            ->whereHas('programLearners', function ($query) {
                $query->whereIn('status', ['enrolled', 'active']);
            })
            ->with('programLearners.program')
            ->get();

        if ($learners->isEmpty()) {
            $this->command->info("No enrolled learners found for company: {$company->name}");
            return;
        }

        $allocatedCount = 0;
        $simCardIndex = 0;

        // Allocate SIM cards to 60-70% of learners
        $learnersToAllocate = $learners->take(ceil($learners->count() * 0.65));

        foreach ($learnersToAllocate as $learner) {
            if ($simCardIndex >= $availableSimCards->count()) {
                break; // No more SIM cards available
            }

            // Get the learner's active program enrollment
            $activeProgramLearner = $learner->programLearners()
                ->whereIn('status', ['enrolled', 'active'])
                ->with('program')
                ->first();

            if (!$activeProgramLearner || !$activeProgramLearner->program) {
                continue;
            }

            $simCard = $availableSimCards[$simCardIndex];
            $program = $activeProgramLearner->program;

            // Determine allocation details
            $allocationDate = now()->subDays(rand(1, 30));
            $chargeAmount = rand(0, 1) ? $simCard->selling_price : 0; // 50% chance of charging
            $paymentRequired = $chargeAmount > 0;

            // Create the allocation
            DB::transaction(function () use ($simCard, $learner, $program, $company, $allocationDate, $chargeAmount, $paymentRequired) {
                $allocation = SimCardAllocation::create([
                    'sim_card_id' => $simCard->id,
                    'user_id' => $learner->id,
                    'program_id' => $program->id,
                    'company_id' => $company->id,
                    'allocated_date' => $allocationDate,
                    'status' => rand(0, 10) > 8 ? 'returned' : 'active', // 80% active, 20% returned
                    'charge_amount' => $chargeAmount,
                    'payment_required' => $paymentRequired,
                    'payment_status' => $paymentRequired ? (rand(0, 10) > 7 ? 'overdue' : 'paid') : 'paid',
                    'allocated_by' => $this->getRandomStaffMember($company),
                    'notes' => $this->getRandomAllocationNote(),
                    'conditions_on_allocation' => [
                        'device_condition' => 'good',
                        'packaging_included' => true,
                        'sim_pin_provided' => true,
                        'manual_provided' => true,
                    ],
                ]);

                // If returned, set return details
                if ($allocation->status === 'returned') {
                    $returnDate = $allocationDate->copy()->addDays(rand(7, 25));
                    $allocation->update([
                        'return_date' => $returnDate,
                        'returned_by' => $this->getRandomStaffMember($company),
                        'return_notes' => $this->getRandomReturnNote(),
                        'conditions_on_return' => [
                            'device_condition' => rand(0, 10) > 7 ? 'damaged' : 'good',
                            'all_accessories_returned' => rand(0, 10) > 2,
                            'sim_card_returned' => true,
                        ],
                    ]);

                    // Update SIM card status back to available if returned
                    $simCard->update(['status' => 'available']);
                } else {
                    // Update SIM card status to allocated
                    $simCard->update([
                        'status' => 'allocated',
                        'activated_at' => $allocationDate,
                    ]);
                }
            });

            $allocatedCount++;
            $simCardIndex++;
        }

        $this->command->info("Created {$allocatedCount} SIM card allocations for company: {$company->name}");
    }

    private function getRandomStaffMember(Company $company): ?int
    {
        $staffMember = User::where('company_id', $company->id)
            ->where('is_employee', true)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['hr_manager', 'company_admin', 'admin']);
            })
            ->inRandomOrder()
            ->first();

        return $staffMember?->id;
    }

    private function getRandomAllocationNote(): string
    {
        $notes = [
            'Allocated for program training',
            'SIM card provided for communication during training',
            'Temporary allocation for program duration',
            'Work-related communication SIM',
            'Training program resource allocation',
            'Allocated as per program requirements',
            'Standard SIM allocation for learner',
            'Emergency contact SIM for training',
        ];

        return $notes[array_rand($notes)];
    }

    private function getRandomReturnNote(): string
    {
        $notes = [
            'Program completed - SIM returned',
            'Learner withdrew from program',
            'Training completed successfully',
            'End of program allocation period',
            'Voluntary return by learner',
            'Program termination - equipment returned',
            'Standard end-of-program return',
            'Learner no longer requires SIM',
        ];

        return $notes[array_rand($notes)];
    }
}
