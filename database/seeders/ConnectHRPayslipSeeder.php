<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ConnectHRPayslipSeeder extends Seeder
{
    public function run(): void
    {
        // Get Connect HR company
        $company = Company::where('paye_reference_number', '7080824016')->first();
        if (!$company) {
            $this->command->error('Connect HR company not found. Please run TestCompanySeeder first.');
            return;
        }

        // Get or create the program
        $program = Program::where('company_id', $company->id)->first();

        // Employee data from IRP5 records
        $employees = [
            // Existing employees that we already have
            ['first_name' => 'Andile', 'last_name' => 'Mdlankomo', 'email' => 'andile.mdlankomo@connecthr.co.za', 'id_number' => '0007095439084', 'employee_number' => 'CHR001', 'phone' => '0896609245', 'birth_date' => '2000-07-09'],
            ['first_name' => 'Ayanda', 'last_name' => 'Thabethe', 'email' => 'ayanda.thabethe@connecthr.co.za', 'id_number' => '9208230852089', 'employee_number' => 'CHR002', 'phone' => '1506763174', 'birth_date' => '1992-08-23'],
            ['first_name' => 'Bongiwe', 'last_name' => 'Nkosi', 'email' => 'bongiwe.nkosi@connecthr.co.za', 'id_number' => '9707180326085', 'employee_number' => 'CHR003', 'phone' => '3978380164', 'birth_date' => '1997-07-18'],
            ['first_name' => 'Kelebogile', 'last_name' => 'Pelo', 'email' => 'kelebogile.pelo@connecthr.co.za', 'id_number' => '0110090253086', 'employee_number' => 'CHR004', 'phone' => '1815638182', 'birth_date' => '2001-10-09'],
            ['first_name' => 'Kwazusomandla', 'last_name' => 'Ndaba', 'email' => 'kwazusomandla.ndaba@connecthr.co.za', 'id_number' => '9505185169082', 'employee_number' => 'CHR005', 'phone' => '1841646175', 'birth_date' => '1995-05-18'],

            // Additional employees from IRP5 data
            ['first_name' => 'Mondli', 'last_name' => 'Mbatha', 'email' => 'mondli.mbatha@connecthr.co.za', 'id_number' => '9504075162083', 'employee_number' => 'CHR006', 'phone' => '3468293174', 'birth_date' => '1995-04-07'],
            ['first_name' => 'Noluthando', 'last_name' => 'Nombewu', 'email' => 'noluthando.nombewu@connecthr.co.za', 'id_number' => '0102100346083', 'employee_number' => 'CHR007', 'phone' => '1855762181', 'birth_date' => '2001-02-10'],
            ['first_name' => 'Nolwazi', 'last_name' => 'Khuthama', 'email' => 'nolwazi.khuthama@connecthr.co.za', 'id_number' => '9912190194081', 'employee_number' => 'CHR008', 'phone' => '3208538177', 'birth_date' => '1999-12-19'],
            ['first_name' => 'Nonhlanhla', 'last_name' => 'Nyathi', 'email' => 'nonhlanhla.nyathi@connecthr.co.za', 'id_number' => '0210260141085', 'employee_number' => 'CHR009', 'phone' => '1647016193', 'birth_date' => '2002-10-26'],
            ['first_name' => 'Phathutshedzo', 'last_name' => 'Matambela', 'email' => 'phathutshedzo.matambela@connecthr.co.za', 'id_number' => '9312295888085', 'employee_number' => 'CHR010', 'phone' => '3106072170', 'birth_date' => '1993-12-29'],
            ['first_name' => 'Sanele', 'last_name' => 'Mthethwa', 'email' => 'sanele.mthethwa@connecthr.co.za', 'id_number' => '0111155345080', 'employee_number' => 'CHR011', 'phone' => '0886609245', 'birth_date' => '2001-11-15'],
            ['first_name' => 'Simon', 'last_name' => 'Lukhele', 'email' => 'simon.lukhele@connecthr.co.za', 'id_number' => '0103035224080', 'employee_number' => 'CHR012', 'phone' => '0425714284', 'birth_date' => '2001-03-03'],
            ['first_name' => 'Siphokazi', 'last_name' => 'Ketsiwe', 'email' => 'siphokazi.ketsiwe@connecthr.co.za', 'id_number' => '9908220191089', 'employee_number' => 'CHR013', 'phone' => '0101844322', 'birth_date' => '1999-08-22'],
            ['first_name' => 'Snegugu', 'last_name' => 'Khuzwayo', 'email' => 'snegugu.khuzwayo@connecthr.co.za', 'id_number' => '9406110745080', 'employee_number' => 'CHR014', 'phone' => '3658343169', 'birth_date' => '1994-06-11'],
            ['first_name' => 'Tebogo', 'last_name' => 'Mojapelo', 'email' => 'tebogo.mojapelo@connecthr.co.za', 'id_number' => '9511070698086', 'employee_number' => 'CHR015', 'phone' => '1782534182', 'birth_date' => '1995-11-07'],
            ['first_name' => 'Thabo', 'last_name' => 'Moshoeng', 'email' => 'thabo.moshoeng@connecthr.co.za', 'id_number' => '9910215090086', 'employee_number' => 'CHR016', 'phone' => '3371987177', 'birth_date' => '1999-10-21'],
            ['first_name' => 'Thabo', 'last_name' => 'Chauke', 'email' => 'thabo.chauke@connecthr.co.za', 'id_number' => '9409095363085', 'employee_number' => 'CHR017', 'phone' => '0606189280', 'birth_date' => '1994-09-09'],
            ['first_name' => 'Thandiwe', 'last_name' => 'Mabusela', 'email' => 'thandiwe.mabusela@connecthr.co.za', 'id_number' => '0311160461084', 'employee_number' => 'CHR018', 'phone' => '2548931183', 'birth_date' => '2003-11-16'],
            ['first_name' => 'Thato', 'last_name' => 'Mohlokoane', 'email' => 'thato.mohlokoane@connecthr.co.za', 'id_number' => '0504171372080', 'employee_number' => 'CHR019', 'phone' => '0127331338', 'birth_date' => '2005-04-17'],
            ['first_name' => 'Tshepo', 'last_name' => 'Tshobeng', 'email' => 'tshepo.tshobeng@connecthr.co.za', 'id_number' => '9612265725088', 'employee_number' => 'CHR020', 'phone' => '0592700256', 'birth_date' => '1996-12-26'],
        ];

        // Create missing employees
        foreach ($employees as $employeeData) {
            // Generate unique employee number
            $uniqueEmployeeNumber = $this->generateUniqueEmployeeNumber($company->id, $employeeData['employee_number']);

            $user = User::firstOrCreate(
                ['email' => $employeeData['email']],
                [
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'email' => $employeeData['email'],
                    'password' => Hash::make('password123'),
                    'phone' => $employeeData['phone'],
                    'company_id' => $company->id,
                    'id_number' => $employeeData['id_number'],
                    'birth_date' => Carbon::parse($employeeData['birth_date']),
                    'employee_number' => $uniqueEmployeeNumber,
                    'res_addr_line1' => '8 Greenstone PI',
                    'res_addr_line2' => 'Stoneridge Office Park',
                    'res_city' => 'Greenstone',
                    'res_postcode' => '1616',
                    'res_country_code' => 'ZAF',
                    'is_active' => true,
                    'is_employee' => true,
                    'employment_status' => 'active',
                    'employment_start_date' => '2024-03-01',
                ]
            );

            // Assign learner role if not already assigned
            if (!$user->hasRole('learner')) {
                $user->assignRole('learner');
            }
        }

        // Create payslips for the employment period (March 2024 to August 2024)
        // Based on IRP5 data: employment from 2024-03-01 to 2024-08-31
        $payPeriods = [
            ['start' => '2024-03-01', 'end' => '2024-03-31', 'pay_date' => '2024-03-31'],
            ['start' => '2024-04-01', 'end' => '2024-04-30', 'pay_date' => '2024-04-30'],
            ['start' => '2024-05-01', 'end' => '2024-05-31', 'pay_date' => '2024-05-31'],
            ['start' => '2024-06-01', 'end' => '2024-06-30', 'pay_date' => '2024-06-30'],
            ['start' => '2024-07-01', 'end' => '2024-07-31', 'pay_date' => '2024-07-31'],
            ['start' => '2024-08-01', 'end' => '2024-08-31', 'pay_date' => '2024-08-31'],
        ];

        $payslipCounter = 0;

        // Get all Connect HR employees
        $allEmployees = User::where('company_id', $company->id)
            ->where('is_employee', true)
            ->get();

        foreach ($allEmployees as $employee) {
            foreach ($payPeriods as $periodIndex => $period) {
                $periodStart = Carbon::parse($period['start']);
                $periodEnd = Carbon::parse($period['end']);
                $payDate = Carbon::parse($period['pay_date']);

                // Monthly gross pay from IRP5: 3883 / 6 months = 647.17
                $monthlyGross = 647.17;
                $paye = round($monthlyGross * 0.18, 2); // Approximately 18% PAYE
                $uifEmployee = round($monthlyGross * 0.01, 2); // 1% UIF employee
                $uifEmployer = round($monthlyGross * 0.01, 2); // 1% UIF employer
                $netPay = $monthlyGross - $paye - $uifEmployee;

                $payslip = Payslip::create([
                    'company_id' => $company->id,
                    'user_id' => $employee->id,
                    'program_id' => $program ? $program->id : null,
                    'payslip_number' => 'PSL' . $employee->employee_number . $periodStart->format('Ym'),
                    'payroll_period_start' => $periodStart,
                    'payroll_period_end' => $periodEnd,
                    'pay_date' => $payDate,
                    'pay_year' => 2024,
                    'pay_month' => $periodStart->month,
                    'pay_period_number' => $periodIndex + 1,
                    'tax_year' => 2024,
                    'tax_month_number' => $periodStart->month,

                    // Earnings
                    'basic_earnings' => $monthlyGross,
                    'gross_earnings' => $monthlyGross,
                    'taxable_earnings' => $monthlyGross,
                    'net_pay' => $netPay,

                    // SARS IRP5 Source Codes
                    'sars_3601' => $monthlyGross, // Basic salary/wages
                    'sars_3699' => $monthlyGross, // Total remuneration for UIF/SDL purposes

                    // Deductions
                    'paye_tax' => $paye,
                    'uif_employee' => $uifEmployee,
                    'uif_employer' => $uifEmployer,
                    'uif_contribution_base' => $monthlyGross,
                    'total_deductions' => $paye + $uifEmployee,

                    // ETI (Employment Tax Incentive) - R1500 from IRP5 data
                    'eti_benefit' => 250.00, // 1500 / 6 months
                    'eti_eligible' => true,

                    // Working days (approximately 22 working days per month)
                    'days_worked' => 22,

                    // Status
                    'status' => 'paid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $payslipCounter++;
            }
        }

        $this->command->info("Created {$payslipCounter} payslips for " . count($employees) . " Connect HR employees (March-August 2024)");
        $this->command->info("Tax Year: 2024 (Employment period: 2024-03-01 to 2024-08-31)");
        $this->command->info("Company: Connect HR (PAYE: 7080824016)");
    }

    private function generateUniqueEmployeeNumber($companyId, $originalEmployeeNumber)
    {
        $employeeNumber = $originalEmployeeNumber;
        $counter = 1;

        // Check if employee number already exists and generate a unique one
        while (User::where('employee_number', $employeeNumber)->exists()) {
            // Extract the number part and add counter
            $numberPart = preg_replace('/[^0-9]/', '', $originalEmployeeNumber);
            $newNumber = (int)$numberPart + ($counter * 1000);
            $employeeNumber = 'CHR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $employeeNumber;
    }
}
