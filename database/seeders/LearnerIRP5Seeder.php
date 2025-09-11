<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LearnerIRP5Seeder extends Seeder
{
    public function run(): void
    {
        // Get Connect HR company specifically 
        $company = Company::where('paye_reference_number', '7080824016')->first();
        if (!$company) {
            $this->command->error('Connect HR company not found. Please run TestCompanySeeder first.');
            return;
        }

        $learners = [
            ['first_name' => 'Andile', 'last_name' => 'Mdlankomo', 'email' => 'andile.mdlankomo@connecthr.co.za', 'id_number' => '0007095439084', 'date_of_birth' => '2000-07-09', 'phone' => '0896609245', 'employee_code' => 'CHR001', 'gender' => 'M'],
            ['first_name' => 'Ayanda', 'last_name' => 'Thabethe', 'email' => 'ayanda.thabethe@connecthr.co.za', 'id_number' => '9208230852089', 'date_of_birth' => '1992-08-23', 'phone' => '1506763174', 'employee_code' => 'CHR002', 'gender' => 'F'],
            ['first_name' => 'Bongiwe', 'last_name' => 'Nkosi', 'email' => 'bongiwe.nkosi@connecthr.co.za', 'id_number' => '9707180326085', 'date_of_birth' => '1997-07-18', 'phone' => '3978380164', 'employee_code' => 'CHR003', 'gender' => 'F'],
            ['first_name' => 'Kelebogile', 'last_name' => 'Pelo', 'email' => 'kelebogile.pelo@connecthr.co.za', 'id_number' => '0110090253086', 'date_of_birth' => '2001-10-09', 'phone' => '1815638182', 'employee_code' => 'CHR004', 'gender' => 'F'],
            ['first_name' => 'Kwazusomandla', 'last_name' => 'Ndaba', 'email' => 'kwazusomandla.ndaba@connecthr.co.za', 'id_number' => '9505185169082', 'date_of_birth' => '1995-05-18', 'phone' => '1841646175', 'employee_code' => 'CHR005', 'gender' => 'M'],
        ];

        foreach ($learners as $learnerData) {
            $user = User::firstOrCreate(
                ['email' => $learnerData['email']],
                [
                    'first_name' => $learnerData['first_name'],
                    'last_name' => $learnerData['last_name'],
                    'email' => $learnerData['email'],
                    'password' => Hash::make('password123'),
                    'phone' => $learnerData['phone'],
                    'company_id' => $company->id,
                    'id_number' => $learnerData['id_number'],
                    'birth_date' => Carbon::parse($learnerData['date_of_birth']),
                    'gender' => $learnerData['gender'] === 'M' ? 'male' : 'female',
                    'race_id' => null, // No races seeded yet
                    'res_addr_line1' => '8 Greenstone PI',
                    'res_addr_line2' => 'Stoneridge Office Park',
                    'res_city' => 'Greenstone',
                    'res_postcode' => '1616',
                    'res_country_code' => 'ZAF',
                    'is_active' => true,
                ]
            );

            $user->assignRole('learner');
            $this->command->info("Created learner: {$user->first_name} {$user->last_name} ({$learnerData['employee_code']})");
        }

        $this->command->info('Successfully created ' . count($learners) . ' learners from SARS IRP5 data.');
    }
}
