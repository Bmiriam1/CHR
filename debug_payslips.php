<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING PAYSLIP CREATION ===\n\n";

// Step 1: Check company
echo "1. Checking company...\n";
$company = Company::first();
if ($company) {
    echo "   ✓ Company found: " . $company->name . " (ID: " . $company->id . ")\n";
} else {
    echo "   ✗ No company found\n";
    exit;
}

// Step 2: Check employees
echo "\n2. Checking employees...\n";
$employees = User::where('employee_number', 'like', 'CHR%')->get();
echo "   Found " . $employees->count() . " CHR employees\n";
if ($employees->count() > 0) {
    echo "   First employee: " . $employees->first()->employee_number . " (ID: " . $employees->first()->id . ")\n";
} else {
    echo "   ✗ No CHR employees found\n";
    exit;
}

// Step 3: Check existing payslips
echo "\n3. Checking existing payslips...\n";
$existingPayslips = Payslip::count();
echo "   Total payslips: " . $existingPayslips . "\n";
$chrPayslips = Payslip::whereHas('user', function ($q) {
    $q->where('employee_number', 'like', 'CHR%');
})->count();
echo "   CHR employee payslips: " . $chrPayslips . "\n";

// Step 4: Try creating a single payslip
echo "\n4. Testing single payslip creation...\n";
$testEmployee = $employees->first();
$testDate = Carbon::parse('2024-03-01');

try {
    $payslip = Payslip::create([
        'company_id' => $company->id,
        'user_id' => $testEmployee->id,
        'program_id' => null,
        'payslip_number' => 'TEST' . time(), // Unique number
        'payroll_period_start' => $testDate,
        'payroll_period_end' => $testDate->endOfMonth(),
        'pay_date' => $testDate->endOfMonth(),
        'pay_year' => $testDate->year,
        'pay_month' => $testDate->month,
        'pay_period_number' => $testDate->month,
        'tax_year' => $testDate->year,
        'tax_month_number' => $testDate->month,
        'basic_earnings' => 3883.00,
        'taxable_earnings' => 3883.00,
        'paye_tax' => 1500.00,
        'uif_employee' => 76.67,
        'uif_employer' => 35.18,
        'sdl_contribution' => 112.85,
        'sars_3601' => 3883.00,
        'sars_3605' => 0.00,
        'sars_3615' => 0.00,
        'sars_3617' => 0.00,
        'sars_3627' => 0.00,
        'sars_3699' => 3883.00,
        'net_pay' => 2383.00,
        'status' => 'processed',
    ]);
    echo "   ✓ Payslip created successfully! ID: " . $payslip->id . "\n";
} catch (Exception $e) {
    echo "   ✗ Error creating payslip: " . $e->getMessage() . "\n";
    echo "   Error details: " . $e->getTraceAsString() . "\n";
}

// Step 5: Check if payslip was actually created
echo "\n5. Verifying payslip creation...\n";
$newPayslipCount = Payslip::count();
echo "   Total payslips now: " . $newPayslipCount . "\n";
if ($newPayslipCount > $existingPayslips) {
    echo "   ✓ Payslip was created successfully!\n";

    // Check the created payslip
    $createdPayslip = Payslip::latest()->first();
    echo "   Created payslip details:\n";
    echo "     - ID: " . $createdPayslip->id . "\n";
    echo "     - Number: " . $createdPayslip->payslip_number . "\n";
    echo "     - Tax Year: " . $createdPayslip->tax_year . "\n";
    echo "     - Pay Year: " . $createdPayslip->pay_year . "\n";
    echo "     - Employee: " . $createdPayslip->user->employee_number . "\n";
} else {
    echo "   ✗ Payslip was not created\n";
}

echo "\n=== DEBUG COMPLETE ===\n";


