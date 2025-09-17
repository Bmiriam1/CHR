<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PaymentScheduleController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SimCardController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Role-based Login (for direct access)
Route::get('/login/{role}', function ($role) {
    if (!in_array($role, ['learner', 'company'])) {
        return redirect()->route('login');
    }
    return view('auth.login-role', compact('role'));
})->name('login.role');

// Landing / Home
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Redirect based on user role
        if ($user->hasRole('learner')) {
            return app(\App\Http\Controllers\DashboardController::class)->index();
        } elseif ($user->hasRole(['admin', 'hr_manager', 'company_admin'])) {
            return app(\App\Http\Controllers\DashboardController::class)->index();
        }

        return app(\App\Http\Controllers\DashboardController::class)->index();
    }

    // Redirect to login if not authenticated
    return redirect()->route('login');
})->name('home');


    // Dashboard
   Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Company/Client Management
    Route::resource('companies', CompanyController::class);
    Route::get('companies/{company}/programs', [CompanyController::class, 'programs'])->name('companies.programs');
    Route::patch('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggleStatus');

    // Program Management
    Route::resource('programs', ProgramController::class);
    Route::post('programs/{program}/duplicate', [ProgramController::class, 'duplicate'])->name('programs.duplicate');
    Route::patch('programs/{program}/activate', [ProgramController::class, 'activate'])->name('programs.activate');
    Route::patch('programs/{program}/deactivate', [ProgramController::class, 'deactivate'])->name('programs.deactivate');

    // Program additional functionality
    Route::get('programs/{program}/schedules', [ProgramController::class, 'schedules'])->name('programs.schedules');
    Route::get('programs/{program}/progress', [ProgramController::class, 'progress'])->name('programs.progress');
    Route::get('programs/{program}/revenue-report', [ProgramController::class, 'revenueReport'])->name('programs.revenue-report');
    Route::get('programs/{program}/client-pack', [ProgramController::class, 'clientPack'])->name('programs.client-pack');
    Route::post('programs/{program}/add-participant', [ProgramController::class, 'addParticipant'])->name('programs.add-participant');
    Route::patch('programs/{program}/participants/{user}/status', [ProgramController::class, 'updateParticipantStatus'])->name('programs.update-participant-status');
    Route::get('programs/{program}/participants/{user}', [ProgramController::class, 'showParticipant'])->name('programs.show-participant');
    Route::post('programs/{program}/documents', [ProgramController::class, 'uploadDocument'])->name('programs.upload-document');

    // SIM Card Management
    Route::resource('sim-cards', SimCardController::class);
    Route::post('sim-cards/allocate', [SimCardController::class, 'allocate'])->name('sim-cards.allocate');
    Route::patch('sim-card-allocations/{allocation}/return', [SimCardController::class, 'returnAllocation'])->name('sim-card-allocations.return');
    Route::get('programs/{program}/sim-allocations', [SimCardController::class, 'getAllocationsForProgram'])->name('programs.sim-allocations');
    Route::get('api/sim-cards/available', [SimCardController::class, 'getAvailableSimCards'])->name('api.sim-cards.available');

    // Schedule Management
    Route::resource('schedules', ScheduleController::class);
    Route::get('schedules-calendar', [ScheduleController::class, 'calendar'])->name('schedules.calendar');
    Route::patch('schedules/{schedule}/start', [ScheduleController::class, 'start'])->name('schedules.start');
    Route::patch('schedules/{schedule}/complete', [ScheduleController::class, 'complete'])->name('schedules.complete');
    Route::patch('schedules/{schedule}/cancel', [ScheduleController::class, 'cancel'])->name('schedules.cancel');
    Route::get('schedules/{schedule}/qr-code', [ScheduleController::class, 'qrCode'])->name('schedules.qr-code');

    // Attendance Management
    Route::resource('attendance', AttendanceController::class);
    Route::get('attendance/pending-proof', [AttendanceController::class, 'pendingProof'])->name('attendance.pending-proof');
    Route::get('attendance/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');
    Route::get('attendance/schedule/{scheduleId?}', [AttendanceController::class, 'showBySchedule'])->name('attendance.schedule');

    // Bulk Operations
    Route::post('attendance/bulk-mark', [AttendanceController::class, 'bulkMark'])->name('attendance.bulk-mark');

    // QR Code Check-in
    Route::post('attendance/qr-check-in', [AttendanceController::class, 'qrCheckIn'])->name('attendance.qr-check-in');

    // Proof Management
    Route::post('attendance/{attendance}/upload-proof', [AttendanceController::class, 'uploadProof'])->name('attendance.upload-proof');
    Route::post('attendance/{attendance}/approve-proof', [AttendanceController::class, 'approveProof'])->name('attendance.approve-proof');
    Route::post('attendance/{attendance}/reject-proof', [AttendanceController::class, 'rejectProof'])->name('attendance.reject-proof');
    Route::get('attendance/{attendance}/download-proof', [AttendanceController::class, 'downloadProof'])->name('attendance.download-proof');

    // Payslip Management
    Route::resource('payslips', PayslipController::class);

    // Distinct route names to avoid duplication
    Route::get('payslips/{payslip}/download', [PayslipController::class, 'download'])
     ->name('payslips.download'); //GET

    Route::post('payslips/generate', [PayslipController::class, 'generate'])
        ->name('payslips.generate');      // POST action

    Route::patch('payslips/{payslip}/approve', [PayslipController::class, 'approve'])->name('payslips.approve');
    Route::patch('payslips/{payslip}/mark-paid', [PayslipController::class, 'markAsPaid'])->name('payslips.mark-paid');
    Route::get('payslips/{payslip}/download', [PayslipController::class, 'download'])->name('payslips.download');
    Route::post('payslips/bulk-generate', [PayslipController::class, 'bulkGenerate'])->name('payslips.bulk-generate');

    // Leave Management System
    Route::prefix('leave')->name('leave-requests.')->group(function () {
        // Employee leave management
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/balances', [LeaveRequestController::class, 'balances'])->name('balances');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('show');
        Route::patch('/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');

        // Management functions (HR/Admin)
        Route::get('/manage/dashboard', [LeaveRequestController::class, 'manage'])->name('manage');
        Route::patch('/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
        Route::patch('/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('reject');

        // SARS reporting
        Route::get('/reports/sars', [LeaveRequestController::class, 'sarsReport'])->name('sars-report');

        // Admin functions
        Route::post('/admin/initialize-balances', [LeaveRequestController::class, 'initializeBalances'])->name('initialize-balances');
    });

    // Payment Schedule Management
    Route::resource('payment-schedules', PaymentScheduleController::class, ['as' => 'payment_schedules']);
    Route::post('payment-schedules/generate', [PaymentScheduleController::class, 'generate'])->name('payment_schedules.generate');
    Route::patch('payment-schedules/{paymentSchedule}/approve', [PaymentScheduleController::class, 'approve'])->name('payment_schedules.approve');
    Route::get('payment-schedules/{paymentSchedule}/export/{format}', [PaymentScheduleController::class, 'export'])->name('payment_schedules.export');

    // Device Management
    Route::resource('devices', DeviceController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::patch('devices/{device}/approve', [DeviceController::class, 'approve'])->name('devices.approve');
    Route::patch('devices/{device}/block', [DeviceController::class, 'block'])->name('devices.block');
    Route::patch('devices/{device}/unblock', [DeviceController::class, 'unblock'])->name('devices.unblock');
    Route::post('devices/register', [DeviceController::class, 'register'])->name('devices.register');

    // Host Management
    Route::resource('hosts', HostController::class);
    Route::post('hosts/{host}/generate-qr', [HostController::class, 'generateQRCode'])->name('hosts.generate-qr');
    Route::get('hosts/{host}/download-qr', [HostController::class, 'downloadQRCode'])->name('hosts.download-qr');
    Route::post('hosts/validate-qr', [HostController::class, 'validateQRCode'])->name('hosts.validate-qr');

    // SARS Compliance & Exports
    Route::prefix('compliance')->name('compliance.')->group(function () {
        Route::get('/', [ComplianceController::class, 'dashboard'])->name('dashboard');

        // SARS Exports
        Route::get('sars/emp201', [ComplianceController::class, 'emp201Form'])->name('sars.emp201.form');
        Route::post('sars/emp201', [ComplianceController::class, 'generateEmp201'])->name('sars.emp201.generate');
        Route::get('sars/emp501', [ComplianceController::class, 'emp501Form'])->name('sars.emp501.form');
        Route::post('sars/emp501', [ComplianceController::class, 'generateEmp501'])->name('sars.emp501.generate');

        // IRP5 & IT3(a) Tax Certificates
        Route::get('tax-certificates', [ComplianceController::class, 'taxCertificatesIndex'])->name('tax_certificates.index');
        Route::post('tax-certificates/irp5', [ComplianceController::class, 'generateIrp5'])->name('tax_certificates.irp5');
        Route::post('tax-certificates/irp5-csv', [ComplianceController::class, 'generateIrp5Csv'])->name('tax_certificates.irp5_csv');
        Route::post('tax-certificates/it3a', [ComplianceController::class, 'generateIt3a'])->name('tax_certificates.it3a');
        Route::post('/compliance/tax-certificates/it3a', [ComplianceController::class, 'generateIt3a'])->name('compliance.tax_certificates.it3a');

        // UIF Exports
        Route::get('uif', [ComplianceController::class, 'uifForm'])->name('uif.form');
        Route::post('uif/monthly-declaration', [ComplianceController::class, 'generateUifDeclaration'])->name('uif.declaration');
        Route::post('uif/annual-reconciliation', [ComplianceController::class, 'generateUifReconciliation'])->name('uif.reconciliation');

        // ETI Claims
        Route::get('eti', [ComplianceController::class, 'etiDashboard'])->name('eti.dashboard');
        Route::post('eti/monthly-claim', [ComplianceController::class, 'generateEtiClaim'])->name('eti.claim');
        Route::get('eti/learner-eligibility', [ComplianceController::class, 'etiEligibility'])->name('eti.eligibility');

        // SDL Claims
        Route::get('sdl', [ComplianceController::class, 'sdlDashboard'])->name('sdl.dashboard');
        Route::post('sdl/monthly-return', [ComplianceController::class, 'generateSdlReturn'])->name('sdl.return');

        // Audit Reports
        Route::get('audit', [ComplianceController::class, 'auditReports'])->name('audit.index');
        Route::get('audit/payroll-register', [ComplianceController::class, 'payrollRegister'])->name('audit.payroll_register');
        Route::get('audit/attendance-summary', [ComplianceController::class, 'attendanceSummary'])->name('audit.attendance_summary');

        // Compliance Checks
        Route::get('checks', [ComplianceController::class, 'complianceChecks'])->name('checks.index');
        Route::post('checks/run-validation', [ComplianceController::class, 'runValidation'])->name('checks.validate');
    });

    // Analytics routes
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
});

// Auth scaffolding
require __DIR__ . '/auth.php';

// Override default login route with custom role selection
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
