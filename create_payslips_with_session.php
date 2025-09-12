<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating payslip data with proper session...\n";

// Get the company and set session
$company = Company::first();
session(['current_company_id' => $company->id]);

echo "Company: " . $company->name . " (ID: " . $company->id . ")\n";
echo "Session company_id: " . session('current_company_id') . "\n";

// Get employees
$employees = User::where('employee_number', 'like', 'CHR%')->get();
echo "Employees: " . $employees->count() . "\n";

// Create payslip data for each employee (6 months of data)
$months = [
    '2024-03-01',
    '2024-04-01',
    '2024-05-01',
    '2024-06-01',
    '2024-07-01',
    '2024-08-01'
];

$payslipCount = 0;
foreach ($employees as $employee) {
    foreach ($months as $month) {
        $monthDate = Carbon::parse($month);

        Payslip::create([
            'company_id' => $company->id,
            'user_id' => $employee->id,
            'program_id' => null,
            'payslip_number' => 'PSL' . $employee->employee_number . $monthDate->format('Ym'),
            'payroll_period_start' => $monthDate->startOfDay(),
            'payroll_period_end' => $monthDate->endOfDay(),
            'pay_date' => $monthDate->endOfDay(),
            'pay_year' => $monthDate->year,
            'pay_month' => $monthDate->month,
            'pay_period_number' => $monthDate->month,
            'tax_year' => $monthDate->year,
            'tax_month_number' => $monthDate->month,
            'basic_earnings' => 3883.00,
            'taxable_earnings' => 3883.00,
            'paye_tax' => 1500.00,
            'uif_employee' => 76.67,
            'uif_employer' => 35.18,
            'sdl_contribution' => 112.85,
            'sars_3601' => 3883.00, // Basic salary
            'sars_3605' => 0.00,     // Overtime
            'sars_3615' => 0.00,     // Bonus
            'sars_3617' => 0.00,     // Commission
            'sars_3627' => 0.00,     // Allowances
            'sars_3699' => 3883.00,  // Total remuneration
            'net_pay' => 2383.00,
            'status' => 'processed',
        ]);
        $payslipCount++;
    }
}

echo "Created " . $payslipCount . " payslips for " . $employees->count() . " employees\n";

// Verify creation
echo "Verifying payslips...\n";
echo "Total payslips: " . Payslip::count() . "\n";
echo "CHR employee payslips: " . Payslip::whereHas('user', function ($q) {
    $q->where('employee_number', 'like', 'CHR%');
})->count() . "\n";

echo "Done!\n";


