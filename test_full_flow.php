<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing full flow: tax certificates page -> IRP5 CSV export...\n";

// Step 1: Simulate accessing the tax certificates page (this should set the session)
echo "\n1. Accessing tax certificates page...\n";
$controller = new \App\Http\Controllers\ComplianceController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('taxCertificatesIndex');
$method->setAccessible(true);

$view = $method->invoke($controller);
echo "Session after page load: " . (session('current_company_id') ?: 'NOT SET') . "\n";

// Step 2: Simulate the IRP5 CSV export request
echo "\n2. Simulating IRP5 CSV export request...\n";
$requestData = [
    'company_id' => '1',
    'tax_year' => '2024',
    'user_ids' => []
];

$company = Company::findOrFail($requestData['company_id']);
$taxYear = $requestData['tax_year'];

echo "Company: " . $company->name . " (ID: " . $company->id . ")\n";
echo "Tax Year: " . $taxYear . "\n";

// The controller should set the session again, but let's check if it's already set
echo "Session before controller: " . (session('current_company_id') ?: 'NOT SET') . "\n";

// Set session for tenant scope to work properly (this is what the controller does)
session(['current_company_id' => $company->id]);
echo "Session after controller: " . session('current_company_id') . "\n";

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
    echo "No payslips found!\n";
}

echo "\n=== TEST COMPLETE ===\n";





