<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging session management...\n";

// Check if session table exists
try {
    $sessions = \DB::table('sessions')->count();
    echo "Sessions table exists with " . $sessions . " sessions\n";
} catch (Exception $e) {
    echo "Sessions table error: " . $e->getMessage() . "\n";
}

// Test session setting and retrieval
echo "\nTesting session...\n";
session(['test_key' => 'test_value']);
echo "Session test_key: " . session('test_key') . "\n";

// Test company session
$company = Company::first();
session(['current_company_id' => $company->id]);
echo "Session current_company_id: " . session('current_company_id') . "\n";

// Test payslip count with session
echo "Payslips count with session: " . Payslip::count() . "\n";

// Clear session and test again
session()->forget('current_company_id');
echo "Payslips count without session: " . Payslip::count() . "\n";

// Set session again
session(['current_company_id' => $company->id]);
echo "Payslips count with session restored: " . Payslip::count() . "\n";

echo "\nSession debugging complete.\n";


