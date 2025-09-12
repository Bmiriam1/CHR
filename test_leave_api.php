<?php

/**
 * Test script for Leave Management API
 * 
 * This script demonstrates how to use the leave application API endpoint.
 * Run with: php test_leave_api.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Program;
use App\Models\LeaveType;
use App\Services\LeaveManagementService;

echo "=== Leave Management API Test ===\n\n";

try {
    // Get test user and authenticate
    $user = User::first();
    if (!$user) {
        echo "❌ No users found in database\n";
        exit(1);
    }

    // Set up authentication
    auth()->login($user);

    echo "✅ User found: {$user->email}\n";
    echo "✅ User authenticated\n";

    // Get program and leave type
    $program = Program::first();
    $leaveType = LeaveType::first();

    if (!$program || !$leaveType) {
        echo "❌ Missing program or leave type data\n";
        exit(1);
    }

    echo "✅ Program: {$program->title}\n";
    echo "✅ Leave Type: {$leaveType->name}\n\n";

    // Test leave application
    echo "=== Testing Leave Application ===\n";

    $service = app(LeaveManagementService::class);

    // Test data with different dates to avoid overlaps
    $testData = [
        'program_id' => $program->id,
        'leave_type_id' => $leaveType->id,
        'start_date' => now()->addDays(30)->format('Y-m-d'),
        'end_date' => now()->addDays(32)->format('Y-m-d'),
        'reason' => 'API Test - Family vacation',
        'notes' => 'Testing the leave application API endpoint',
        'is_emergency' => false
    ];

    echo "📝 Test Data:\n";
    echo "   Program ID: {$testData['program_id']}\n";
    echo "   Leave Type ID: {$testData['leave_type_id']}\n";
    echo "   Start Date: {$testData['start_date']}\n";
    echo "   End Date: {$testData['end_date']}\n";
    echo "   Reason: {$testData['reason']}\n\n";

    // Process leave request
    $result = $service->processLeaveRequest($testData);

    if ($result['success']) {
        $leaveRequest = $result['leave_request'];
        echo "✅ Leave request created successfully!\n";
        echo "   ID: {$leaveRequest->id}\n";
        echo "   Status: {$leaveRequest->status}\n";
        echo "   Duration: {$leaveRequest->duration} days\n";
        echo "   Requires Medical Certificate: " . ($leaveRequest->requires_medical_certificate ? 'Yes' : 'No') . "\n";
        echo "   Is Paid Leave: " . ($leaveRequest->is_paid_leave ? 'Yes' : 'No') . "\n";
        echo "   Daily Rate: R" . number_format($leaveRequest->daily_rate_at_time, 2) . "\n";
        echo "   Total Pay: R" . number_format($leaveRequest->total_leave_pay, 2) . "\n";
        echo "   Submitted: {$leaveRequest->submitted_at}\n";

        if (!empty($result['validation']['warnings'])) {
            echo "\n⚠️  Warnings:\n";
            foreach ($result['validation']['warnings'] as $warning) {
                echo "   - {$warning}\n";
            }
        }
    } else {
        echo "❌ Leave request failed:\n";
        foreach ($result['errors'] as $error) {
            echo "   - {$error}\n";
        }
    }

    echo "\n=== API Endpoint Information ===\n";
    echo "Endpoint: POST /api/leave/requests\n";
    echo "Authentication: Bearer Token (Laravel Sanctum)\n";
    echo "Content-Type: application/json\n\n";

    // Generate sample token for testing
    $token = $user->createToken('test-token');
    echo "Sample Token: {$token->plainTextToken}\n\n";

    echo "Sample cURL command:\n";
    echo "curl -X POST http://localhost:8000/api/leave/requests \\\n";
    echo "  -H \"Authorization: Bearer {$token->plainTextToken}\" \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"program_id\": {$program->id},\n";
    echo "    \"leave_type_id\": {$leaveType->id},\n";
    echo "    \"start_date\": \"{$testData['start_date']}\",\n";
    echo "    \"end_date\": \"{$testData['end_date']}\",\n";
    echo "    \"reason\": \"{$testData['reason']}\",\n";
    echo "    \"notes\": \"{$testData['notes']}\",\n";
    echo "    \"is_emergency\": false\n";
    echo "  }'\n\n";

    echo "✅ Test completed successfully!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
