<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING PAYSLIP CREATION V2 ===\n\n";

$company = Company::first();
$testEmployee = User::where('employee_number', 'like', 'CHR%')->first();

echo "Testing different date formats...\n";

// Test 1: Using startOfDay() and endOfDay()
echo "\n1. Testing with startOfDay() and endOfDay()...\n";
$testDate = Carbon::parse('2024-03-01');
try {
    $payslip = Payslip::create([
        'company_id' => $company->id,
        'user_id' => $testEmployee->id,
        'program_id' => null,
        'payslip_number' => 'TEST1' . time(),
        'payroll_period_start' => $testDate->startOfDay(),
        'payroll_period_end' => $testDate->endOfDay(),
        'pay_date' => $testDate->endOfDay(),
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
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Using just the date string
echo "\n2. Testing with date strings...\n";
try {
    $payslip = Payslip::create([
        'company_id' => $company->id,
        'user_id' => $testEmployee->id,
        'program_id' => null,
        'payslip_number' => 'TEST2' . time(),
        'payroll_period_start' => '2024-04-01',
        'payroll_period_end' => '2024-04-30',
        'pay_date' => '2024-04-30',
        'pay_year' => 2024,
        'pay_month' => 4,
        'pay_period_number' => 4,
        'tax_year' => 2024,
        'tax_month_number' => 4,
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
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Check final count
echo "\n3. Final payslip count: " . Payslip::count() . "\n";

echo "\n=== DEBUG COMPLETE ===\n";





