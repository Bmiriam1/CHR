<?php

namespace Database\Seeders;

use App\Models\SimCard;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->info('No companies found. Please run company seeders first.');
            return;
        }

        $this->command->info('Creating SIM cards for companies...');

        foreach ($companies as $company) {
            $this->createSimCardsForCompany($company);
        }

        $this->command->info('SIM card seeding completed.');
    }

    private function createSimCardsForCompany(Company $company): void
    {
        $simCardsData = [
            [
                'phone_number' => '+27821234567',
                'serial_number' => 'TEL001234567890',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Initial batch of SIM cards',
                'purchased_at' => now()->subDays(30),
            ],
            [
                'phone_number' => '+27827654321',
                'serial_number' => 'TEL098765432109',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Initial batch of SIM cards',
                'purchased_at' => now()->subDays(30),
            ],
            [
                'phone_number' => '+27829876543',
                'serial_number' => 'TEL567890123456',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Initial batch of SIM cards',
                'purchased_at' => now()->subDays(25),
            ],
            [
                'phone_number' => '+27823456789',
                'serial_number' => 'TEL345678901234',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Second batch of SIM cards',
                'purchased_at' => now()->subDays(20),
            ],
            [
                'phone_number' => '+27826543210',
                'serial_number' => 'TEL654321098765',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Second batch of SIM cards',
                'purchased_at' => now()->subDays(20),
            ],
            [
                'phone_number' => '+27828765432',
                'serial_number' => 'TEL876543210987',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Third batch of SIM cards',
                'purchased_at' => now()->subDays(15),
            ],
            [
                'phone_number' => '+27824567890',
                'serial_number' => 'TEL456789012345',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Third batch of SIM cards',
                'purchased_at' => now()->subDays(15),
            ],
            [
                'phone_number' => '+27825678901',
                'serial_number' => 'TEL567890123456',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Fourth batch of SIM cards',
                'purchased_at' => now()->subDays(10),
            ],
            [
                'phone_number' => '+27827890123',
                'serial_number' => 'TEL789012345678',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Fourth batch of SIM cards',
                'purchased_at' => now()->subDays(10),
            ],
            [
                'phone_number' => '+27829012345',
                'serial_number' => 'TEL901234567890',
                'cost_price' => 150.00,
                'selling_price' => 200.00,
                'notes' => 'Latest batch of SIM cards',
                'purchased_at' => now()->subDays(5),
            ]
        ];

        foreach ($simCardsData as $index => $simCardData) {
            // Make phone numbers and serial numbers unique per company
            $companyPrefix = str_pad($company->id, 2, '0', STR_PAD_LEFT);
            $simCardData['phone_number'] = '+2782' . $companyPrefix . substr($simCardData['phone_number'], -5);
            $simCardData['serial_number'] = 'TEL' . $companyPrefix . substr($simCardData['serial_number'], -10);

            $existingSimCard = SimCard::where('phone_number', $simCardData['phone_number'])
                ->orWhere('serial_number', $simCardData['serial_number'])
                ->first();

            if (!$existingSimCard) {
                SimCard::create(array_merge($simCardData, [
                    'company_id' => $company->id,
                    'service_provider' => 'Telkom',
                    'status' => 'available',
                    'is_active' => true,
                    'activated_at' => now()->subDays(rand(1, 20)),
                ]));
            }
        }

        $this->command->info("Created SIM cards for company: {$company->name}");
    }
}
