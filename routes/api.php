<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController;

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/change-password', [AuthController::class, 'changePassword']);

    // Logout routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
});

// Attendance API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Check-in/out
    Route::post('/attendance/check-in', [AttendanceApiController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceApiController::class, 'checkOut']);

    // Programs
    Route::get('/programs', [AttendanceApiController::class, 'getPrograms']);
    Route::get('/programs/with-schedules', [AttendanceApiController::class, 'getProgramsWithSchedules']);
    Route::get('/user/programs', [AttendanceApiController::class, 'getUserPrograms']);
    Route::get('/user/primary-program', [AttendanceApiController::class, 'getPrimaryProgram']);

    // Schedules
    Route::get('/schedule/current', [AttendanceApiController::class, 'getCurrentSchedule']);

    // Hosts and Locations
    Route::get('/hosts', [AttendanceApiController::class, 'getHosts']);
    Route::get('/hosts/nearest', [AttendanceApiController::class, 'getNearestHost']);
    Route::post('/hosts/validate-qr', [AttendanceApiController::class, 'validateQRCode']);

    // Attendance data
    Route::get('/attendance/summary', [AttendanceApiController::class, 'getAttendanceSummary']);
    Route::get('/attendance/user/{user_id}', [AttendanceApiController::class, 'getUserAttendance']);

});
