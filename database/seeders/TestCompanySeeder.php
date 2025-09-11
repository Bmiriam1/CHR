<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramType;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing company with PAYE reference 7123456789
        $company = Company::where('paye_reference_number', '7123456789')->first();

        // Create or update admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@connecthr.co.za'],
            [
                'first_name' => 'Kyle',
                'last_name' => 'Mabaso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'ADM001',
                'id_number' => '8001011234567',
                'tax_number' => 'TAX123456789',
                'phone' => '+27 11 123 4568',
                'birth_date' => '1980-01-01',
                'employment_start_date' => '2023-01-01',
                'res_addr_line1' => '456 Admin Street',
                'res_suburb' => 'Sandton',
                'res_city' => 'Johannesburg',
                'res_postcode' => '2196',
                'is_employee' => true,
                'employment_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create learner user
        $learner = User::firstOrCreate(
            ['email' => 'learner@connecthr.co.za'],
            [
                'first_name' => 'Michaela',
                'last_name' => 'McRowdie',
                'email' => 'learner@connecthr.co.za',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'CHR001',
                'id_number' => '9501011234567',
                'tax_number' => 'TAX987654321',
                'phone' => '+27 11 123 4569',
                'birth_date' => '1995-01-01',
                'employment_start_date' => '2024-03-01',
                'res_addr_line1' => '789 Learner Avenue',
                'res_suburb' => 'Rosebank',
                'res_city' => 'Johannesburg',
                'res_postcode' => '2196',
                'is_employee' => true,
                'employment_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create program type
        $programType = ProgramType::firstOrCreate(
            ['name' => 'Skills Development'],
            [
                'slug' => 'skills-development',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create test program
        $program = Program::firstOrCreate(
            ['program_code' => 'DSDP2024'],
            [
                'title' => 'Digital Skills Development Program',
                'program_code' => 'DSDP2024',
                'description' => 'Comprehensive digital skills training program',
                'company_id' => $company->id,
                'program_type_id' => $programType->id,
                'creator_id' => $admin->id,
                'start_date' => '2024-03-01',
                'end_date' => '2024-12-31',
                'daily_rate' => 350.00,
                'transport_allowance' => 50.00,
                'payment_frequency' => 'monthly',
                'payment_day_of_month' => 25,
                'max_learners' => 50,
                'min_learners' => 5,
                'location_type' => 'hybrid',
                'venue' => 'Connect HR Training Center',
                'venue_address' => '123 Business Park Drive, Sandton',
                'section_12h_eligible' => true,
                'section_12h_contract_number' => 'SEC12H2024001',
                'section_12h_start_date' => '2024-03-01',
                'section_12h_end_date' => '2024-12-31',
                'section_12h_allowance' => 1000.00,
                'eti_eligible_program' => true,
                'eti_category' => 'youth',
                'nqf_level' => 4,
                'saqa_id' => '12345',
                'qualification_title' => 'Certificate in Digital Skills',
                'status' => 'active',
                'is_approved' => true,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Create payslips for testing IRP5 export.
     */
    private function createPayslips($company, $learners, $program)
    {
        // Create payslips for March 2024 to December 2024 (partial tax year for testing)
        $months = [
            '2024-03-01',
            '2024-04-01',
            '2024-05-01',
            '2024-06-01',
            '2024-07-01',
            '2024-08-01',
            '2024-09-01',
            '2024-10-01',
            '2024-11-01',
            '2024-12-01'
        ];

        foreach ($learners as $learner) {
            foreach ($months as $monthStart) {
                $payDate = Carbon::parse($monthStart)->endOfMonth();

                // Calculate monthly amounts
                $grossSalary = $program->daily_rate * 22; // 22 working days
                $transportAllowance = $program->transport_allowance * 22;
                $totalGross = $grossSalary + $transportAllowance;

                // Tax calculations (simplified)
                $taxableEarnings = $grossSalary; // Transport not taxable up to limit
                $payeTax = $this->calculatePAYE($taxableEarnings);
                $uifEmployee = min($taxableEarnings * 0.01, 177.12); // 1% capped at R177.12
                $uifEmployer = $uifEmployee; // Employer matches
                $sdlContribution = $taxableEarnings * 0.01; // 1% SDL

                // ETI benefit calculation (youth employment incentive)
                $etiMonth = Carbon::parse($monthStart)->diffInMonths(Carbon::parse($learner->employment_start_date));
                $etiBenefit = 0;
                if ($etiMonth < 12) {
                    $etiBenefit = min(1000, $payeTax); // R1000 or PAYE, whichever is lower
                } elseif ($etiMonth < 24) {
                    $etiBenefit = min(500, $payeTax); // R500 or PAYE, whichever is lower
                }

                $netPayeTax = max(0, $payeTax - $etiBenefit);
                $totalDeductions = $netPayeTax + $uifEmployee;
                $netAmount = $totalGross - $totalDeductions;

                Payslip::create([
                    'user_id' => $learner->id,
                    'company_id' => $company->id,
                    'program_id' => $program->id,
                    'payslip_number' => 'PAY-' . $learner->id . '-' . Carbon::parse($monthStart)->format('Y-m'),
                    'payroll_period_start' => $monthStart,
                    'payroll_period_end' => Carbon::parse($monthStart)->endOfMonth()->toDateString(),
                    'pay_date' => $payDate->toDateString(),
                    'gross_salary' => $grossSalary,
                    'transport_allowance' => $transportAllowance,
                    'total_gross' => $totalGross,
                    'taxable_earnings' => $taxableEarnings,
                    'paye_tax' => $payeTax,
                    'uif_employee' => $uifEmployee,
                    'uif_employer' => $uifEmployer,
                    'uif_contribution_base' => $taxableEarnings,
                    'sdl_contribution' => $sdlContribution,
                    'eti_benefit' => $etiBenefit,
                    'total_deductions' => $totalDeductions,
                    'net_amount' => $netAmount,
                    'status' => 'paid',
                    'is_final' => true,
                    // SARS codes for IRP5
                    'sars_3601' => $taxableEarnings, // Taxable income
                    'sars_3605' => 0, // Other income
                    'sars_3615' => 0, // Pension fund contributions
                    'sars_3617' => 0, // Retirement annuity contributions
                    'sars_3627' => 0, // Medical aid contributions
                    'sars_3699' => $payeTax, // PAYE deducted
                    'created_at' => Carbon::parse($monthStart),
                    'updated_at' => Carbon::parse($monthStart),
                ]);
            }
        }
    }

    /**
     * Calculate PAYE tax (simplified South African tax calculation).
     */
    private function calculatePAYE($monthlyTaxableIncome)
    {
        // 2024/2025 tax brackets (monthly)
        $annualIncome = $monthlyTaxableIncome * 12;

        if ($annualIncome <= 95750) {
            return 0; // Tax-free threshold
        } elseif ($annualIncome <= 237100) {
            $monthlyTax = (($annualIncome - 95750) * 0.18) / 12;
        } elseif ($annualIncome <= 370500) {
            $monthlyTax = ((25443 + (($annualIncome - 237100) * 0.26)) / 12);
        } elseif ($annualIncome <= 512800) {
            $monthlyTax = ((60132 + (($annualIncome - 370500) * 0.31)) / 12);
        } elseif ($annualIncome <= 673000) {
            $monthlyTax = ((104245 + (($annualIncome - 512800) * 0.36)) / 12);
        } elseif ($annualIncome <= 857900) {
            $monthlyTax = ((161917 + (($annualIncome - 673000) * 0.39)) / 12);
        } else {
            $monthlyTax = ((234216 + (($annualIncome - 857900) * 0.41)) / 12);
        }

        // Apply primary rebate (monthly)
        $primaryRebate = 17235 / 12; // R17,235 annually

        return max(0, $monthlyTax - $primaryRebate);
    }
}
