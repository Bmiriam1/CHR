<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\User;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing authenticated request...\n";

// Get a user to authenticate as
$user = User::where('employee_number', 'like', 'CHR%')->first();
if (!$user) {
    echo "No CHR user found for authentication\n";
    exit;
}

echo "Authenticating as: " . $user->employee_number . "\n";

// Authenticate the user
Auth::login($user);

// Create a mock request with CSRF token
$request = Request::create('/compliance/tax-certificates/irp5-csv', 'POST', [
    'company_id' => '1',
    'tax_year' => '2024',
    'user_ids' => []
], [], [], [
    'HTTP_X_CSRF_TOKEN' => csrf_token(),
    'HTTP_REFERER' => 'http://localhost:8000/compliance/tax-certificates'
]);

echo "Request data: " . json_encode($request->all()) . "\n";
echo "CSRF token: " . csrf_token() . "\n";

// Test the actual controller method
$controller = new \App\Http\Controllers\ComplianceController();

try {
    $response = $controller->generateIrp5Csv($request);

    echo "Response status: " . $response->getStatusCode() . "\n";

    $content = $response->getContent();
    echo "Response content length: " . strlen($content) . "\n";

    // Count lines
    $lines = explode("\n", $content);
    echo "Total lines: " . count($lines) . "\n";
    echo "Company header lines: " . (substr_count($content, '2010,') ?: 0) . "\n";
    echo "Employee record lines: " . (substr_count($content, '3010,') ?: 0) . "\n";

    // Show first few lines
    echo "\nFirst 3 lines:\n";
    for ($i = 0; $i < min(3, count($lines)); $i++) {
        echo ($i + 1) . ": " . substr($lines[$i], 0, 100) . "...\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}





