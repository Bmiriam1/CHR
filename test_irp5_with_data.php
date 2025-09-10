<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test IRP5 CSV export with seeded data
echo "Testing IRP5 CSV export with seeded data...\n";

// Get the company and employees
$company = Company::first();
echo "Company: " . $company->name . " (PAYE: " . $company->paye_reference_number . ")\n";

$employees = User::where('employee_number', 'like', 'CHR%')->get();
echo "Found " . $employees->count() . " employees\n";

// Get payslips for 2024
$payslips = Payslip::whereHas('user', function ($query) {
    $query->where('employee_number', 'like', 'CHR%');
})->where('tax_year', 2024)->get();

echo "Found " . $payslips->count() . " payslips for 2024\n";

// Test the CSV generation
$controller = new \App\Http\Controllers\ComplianceController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateIrp5CsvContent');
$method->setAccessible(true);

$csv = $method->invoke($controller, $payslips, $company, 2024);

echo "CSV generated successfully!\n";
echo "Length: " . strlen($csv) . " characters\n";
echo "First 500 characters:\n";
echo substr($csv, 0, 500) . "...\n";

// Save to file for inspection
file_put_contents('test_irp5_with_data_output.csv', $csv);
echo "CSV saved to test_irp5_with_data_output.csv\n";

// Count lines
$lines = explode("\n", $csv);
echo "Total lines: " . count($lines) . "\n";
echo "Company header lines: " . (substr_count($csv, '2010,') ?: 0) . "\n";
echo "Employee record lines: " . (substr_count($csv, '3010,') ?: 0) . "\n";

