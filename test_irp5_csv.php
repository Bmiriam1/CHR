<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test CSV generation
echo "Testing IRP5 CSV generation...\n";

// Create a mock controller instance
$controller = new \App\Http\Controllers\ComplianceController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateIrp5CsvContent');
$method->setAccessible(true);

// Create mock data
$company = new Company();
$company->name = 'Test Company';
$company->paye_reference_number = '1234567890';

$user = new User();
$user->employee_number = 'EMP001';
$user->first_name = 'John';
$user->last_name = 'Doe';
$user->id_number = '9001015009087';
$user->tax_number = 'TAX123456';
$user->employment_start_date = Carbon::parse('2024-03-01');
$user->employment_end_date = null;

$payslip = new Payslip();
$payslip->user_id = 1;
$payslip->taxable_earnings = 15000.00;
$payslip->paye_tax = 1500.00;
$payslip->uif_employee = 150.00;
$payslip->uif_employer = 150.00;
$payslip->sdl_contribution = 75.00;
$payslip->sars_3601 = 12000.00;
$payslip->sars_3605 = 0.00;
$payslip->sars_3615 = 1000.00;
$payslip->sars_3617 = 2000.00;
$payslip->sars_3627 = 0.00;
$payslip->sars_3699 = 15000.00;
$payslip->user = $user;

$payslips = collect([$payslip]);

// Generate CSV
$csv = $method->invoke($controller, $payslips, $company, 2024);

echo "CSV generated successfully!\n";
echo "Length: " . strlen($csv) . " characters\n";
echo "First 500 characters:\n";
echo substr($csv, 0, 500) . "...\n";

// Save to file for inspection
file_put_contents('test_irp5_output.csv', $csv);
echo "CSV saved to test_irp5_output.csv\n";

