<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test SARS format CSV generation
echo "Testing SARS e@syfile format...\n";

// Create a mock controller instance
$controller = new \App\Http\Controllers\ComplianceController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateIrp5CsvContent');
$method->setAccessible(true);

// Create mock company data
$company = new Company();
$company->name = 'Connect HR';
$company->paye_reference_number = '7080824016';
$company->uif_reference_number = 'U080824016';
$company->contact_person = 'Natalie';
$company->contact_surname = 'De Lange';
$company->contact_title = 'Accountant';
$company->phone = '0118495307';
$company->email = 'natalie@gtadmin.co.za';
$company->address_line1 = 'Greenstone PI';
$company->address_line2 = 'Stoneridge Office Park';
$company->suburb = 'Greenstone';
$company->city = 'Greenstone';
$company->postal_code = '1616';
$company->registration_number = '62020';

// Create mock user data
$user = new User();
$user->employee_number = 'CHR001';
$user->first_name = 'Andile';
$user->last_name = 'Mdlankomo';
$user->initials = 'A';
$user->id_number = '0007095439084';
$user->birth_date = Carbon::parse('2000-07-09');
$user->tax_number = '0896609245';
$user->phone = '0118495307';
$user->res_addr_line1 = 'Greenstone PI';
$user->res_addr_line2 = 'Stoneridge Office Park';
$user->res_suburb = 'Greenstone';
$user->res_city = 'Greenstone';
$user->res_postcode = '1616';
$user->employment_start_date = Carbon::parse('2024-03-01');
$user->employment_end_date = Carbon::parse('2024-08-31');

// Create mock payslip data
$payslip = new Payslip();
$payslip->user_id = 1;
$payslip->taxable_earnings = 3883.00;
$payslip->paye_tax = 1500.00;
$payslip->uif_employee = 76.67;
$payslip->uif_employer = 35.18;
$payslip->sdl_contribution = 112.85;
$payslip->sars_3601 = 3883.00;
$payslip->sars_3605 = 0.00;
$payslip->sars_3615 = 0.00;
$payslip->sars_3617 = 0.00;
$payslip->sars_3627 = 0.00;
$payslip->sars_3699 = 3883.00;
$payslip->user = $user;

$payslips = collect([$payslip]);

// Generate CSV
$csv = $method->invoke($controller, $payslips, $company, 2024);

echo "SARS format CSV generated successfully!\n";
echo "Length: " . strlen($csv) . " characters\n";
echo "First 1000 characters:\n";
echo substr($csv, 0, 1000) . "...\n";

// Save to file for inspection
file_put_contents('test_sars_format_output.csv', $csv);
echo "CSV saved to test_sars_format_output.csv\n";

