<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging web request simulation...\n";

// Simulate the exact request that would come from the web form
$requestData = [
    'company_id' => '1',
    'tax_year' => '2024',
    'user_ids' => [] // Empty array means all employees
];

echo "Request data: " . json_encode($requestData) . "\n";

// Validate the request (like the controller does)
$company = Company::findOrFail($requestData['company_id']);
$taxYear = $requestData['tax_year'];

echo "Company: " . $company->name . " (ID: " . $company->id . ")\n";
echo "Tax Year: " . $taxYear . "\n";

// Set session for tenant scope to work properly
session(['current_company_id' => $company->id]);
echo "Session set for company_id: " . session('current_company_id') . "\n";

// This is exactly what the controller does
$startDate = Carbon::create($taxYear, 3, 1);
$endDate = Carbon::create($taxYear + 1, 2, 28)->endOfMonth();

echo "Date range: " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d') . "\n";

$query = Payslip::where('company_id', $company->id)
    ->whereBetween('pay_date', [$startDate, $endDate])
    ->where('is_final', true)
    ->with('user');

if (!empty($requestData['user_ids'])) {
    $query->whereIn('user_id', $requestData['user_ids']);
}

$payslips = $query->get();

echo "Found " . $payslips->count() . " payslips\n";

if ($payslips->count() > 0) {
    echo "First payslip: " . $payslips->first()->payslip_number . " for " . $payslips->first()->user->employee_number . "\n";

    // Test CSV generation
    $controller = new \App\Http\Controllers\ComplianceController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('generateIrp5CsvContent');
    $method->setAccessible(true);

    $csv = $method->invoke($controller, $payslips, $company, $taxYear);

    echo "CSV generated successfully!\n";
    echo "Length: " . strlen($csv) . " characters\n";

    // Count lines
    $lines = explode("\n", $csv);
    echo "Total lines: " . count($lines) . "\n";
    echo "Company header lines: " . (substr_count($csv, '2010,') ?: 0) . "\n";
    echo "Employee record lines: " . (substr_count($csv, '3010,') ?: 0) . "\n";

    // Show first few lines
    echo "\nFirst 3 lines:\n";
    for ($i = 0; $i < min(3, count($lines)); $i++) {
        echo ($i + 1) . ": " . substr($lines[$i], 0, 100) . "...\n";
    }
} else {
    echo "No payslips found! Let's debug...\n";

    // Debug the query step by step
    echo "All payslips for company: " . Payslip::where('company_id', $company->id)->count() . "\n";
    echo "Payslips in date range: " . Payslip::where('company_id', $company->id)->whereBetween('pay_date', [$startDate, $endDate])->count() . "\n";
    echo "Payslips with is_final=true: " . Payslip::where('company_id', $company->id)->where('is_final', true)->count() . "\n";
    echo "Payslips with all conditions: " . Payslip::where('company_id', $company->id)->whereBetween('pay_date', [$startDate, $endDate])->where('is_final', true)->count() . "\n";

    // Check if there are any payslips at all
    echo "Total payslips in database: " . Payslip::count() . "\n";
}

