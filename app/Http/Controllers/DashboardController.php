<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Program;
use App\Models\Payslip;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Schedule;
use App\Models\ProgramBudget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
{
    $user = Auth::user();

    // Get user's company for tenant filtering
    $companyId = $user->company_id;

    if (!$companyId) {
        return view('dashboard', [
            'error' => 'User not assigned to a company. Please contact administrator.',
            'recentActivities' => collect(),
            'metrics' => [],
            'trends' => [],
            'upcomingSchedules' => [],
            'complianceMetrics' => [],
            'previousMetrics' => [],
        ]);
    }

    // Current month data
    $currentMonth = now();
    $startOfMonth = $currentMonth->startOfMonth()->copy();
    $endOfMonth = $currentMonth->endOfMonth()->copy();

    // Previous month for comparison
    $previousMonth = now()->subMonth();
    $startOfPrevMonth = $previousMonth->startOfMonth()->copy();
    $endOfPrevMonth = $previousMonth->endOfMonth()->copy();

    // Get meaningful HR metrics
    $metrics = $this->getHRMetrics($companyId, $startOfMonth, $endOfMonth);
    $previousMetrics = $this->getHRMetrics($companyId, $startOfPrevMonth, $endOfPrevMonth);

    // Calculate trends
    $trends = $this->calculateTrends($metrics, $previousMetrics);

    // Recent activities
    $recentActivities = $this->getRecentActivities($companyId);

    // Upcoming schedules (only if user has programs)
    $upcomingSchedules = collect();
    if ($user->programs()->exists()) {
        $upcomingSchedules = $this->getUpcomingSchedules($user);
    }

    // ETI and compliance metrics
    $complianceMetrics = $this->getComplianceMetrics($companyId, $currentMonth);

    // Choose view based on user role
    if ($user->hasRole('learner')) {
        return view('learner.dashboard', compact(
            'metrics',
            'trends',
            'recentActivities',
            'upcomingSchedules',
            'complianceMetrics',
            'previousMetrics'
        ));
    }

    // Default dashboard for other roles
    return view('dashboard', compact(
        'metrics',
        'trends',
        'recentActivities',
        'upcomingSchedules',
        'complianceMetrics',
        'previousMetrics'
    ));
} //fixed this

    private function getHRMetrics($companyId, $startDate, $endDate)
    {
        return [
            // Employee metrics
            'total_employees' => User::where('company_id', $companyId)
                ->where('is_employee', true)
                ->where('employment_status', 'active')
                ->count(),

            'new_hires' => User::where('company_id', $companyId)
                ->where('is_employee', true)
                ->whereBetween('employment_start_date', [$startDate, $endDate])
                ->count(),

            // Program metrics  
            'active_programs' => Program::where('company_id', $companyId)
                ->where('status', 'active')
                ->count(),

            'total_learners' => User::where('company_id', $companyId)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'learner');
                })
                ->where('employment_status', 'active')
                ->count(),

            // Payroll metrics
            'payslips_generated' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->count(),

            'total_gross_pay' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->sum('gross_earnings'),

            'total_net_pay' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->sum('net_pay'),

            'total_paye' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->sum('paye_tax'),

            'total_uif' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->sum(DB::raw('uif_employee + uif_employer')),

            'total_eti_benefit' => Payslip::where('company_id', $companyId)
                ->whereMonth('pay_date', $startDate->month)
                ->whereYear('pay_date', $startDate->year)
                ->sum('eti_benefit'),

            // Attendance metrics
            'attendance_records' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->count(),

            'present_days' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count(),

            'absent_days' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->whereIn('status', ['absent_unauthorized', 'absent_authorized'])
                ->count(),

            // Leave metrics  
            'leave_requests' => LeaveRequest::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('start_date', [$startDate, $endDate])
                ->count(),

            'approved_leave' => LeaveRequest::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('start_date', [$startDate, $endDate])
                ->where('status', 'approved')
                ->count(),

            // Schedule metrics
            'total_schedules' => Schedule::where('company_id', $companyId)
                ->whereBetween('session_date', [$startDate, $endDate])
                ->count(),

            'completed_schedules' => Schedule::where('company_id', $companyId)
                ->whereBetween('session_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),

            'upcoming_schedules' => Schedule::where('company_id', $companyId)
                ->where('session_date', '>=', now())
                ->where('status', 'scheduled')
                ->count(),

            // Budget metrics
            'active_budgets' => ProgramBudget::where('company_id', $companyId)
                ->where('is_active', true)
                ->count(),

            'total_budget_allocated' => ProgramBudget::where('company_id', $companyId)
                ->where('is_active', true)
                ->sum('total_budget'),

            'total_budget_used' => ProgramBudget::where('company_id', $companyId)
                ->where('is_active', true)
                ->sum('used_budget'),

            'budget_utilization' => $this->calculateBudgetUtilization($companyId),

            // Attendance status breakdown
            'present_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count(),

            'late_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'late')
                ->count(),

            'absent_authorized' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'absent_authorized')
                ->count(),

            'absent_unauthorized' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'absent_unauthorized')
                ->count(),

            'excused_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'excused')
                ->count(),

            'on_leave_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'on_leave')
                ->count(),

            'sick_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'sick')
                ->count(),

            // Payment metrics
            'payable_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('is_payable', true)
                ->count(),

            'non_payable_attendance' => AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('is_payable', false)
                ->count(),
        ];
    }

    private function calculateTrends($current, $previous)
    {
        $trends = [];

        foreach ($current as $key => $value) {
            $prevValue = $previous[$key] ?? 0;

            if ($prevValue > 0) {
                $change = (($value - $prevValue) / $prevValue) * 100;
            } else {
                $change = $value > 0 ? 100 : 0;
            }

            $trends[$key] = [
                'value' => $value,
                'change' => round($change, 1),
                'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same')
            ];
        }

        return $trends;
    }

    private function getRecentActivities($companyId, $limit = 10)
    {
        $activities = collect();

        // Recent payslips - FIXED: Added missing foreach loop and proper array structure
        $recentPayslips = Payslip::where('company_id', $companyId)
            ->with('user')
            ->latest('created_at')
            ->limit(3)
            ->get();

        // FIX: Added the missing foreach loop that was causing the syntax error
        foreach ($recentPayslips as $payslip) {
            $activities->push([
                'type' => 'payslip',
                'id' => $payslip->id, // Added ID for download link
                'title' => 'Payslip generated',
                'description' => $payslip->user->first_name . ' ' . $payslip->user->last_name,
                'date' => $payslip->created_at,
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'success'
            ]);
        }

        // Recent attendance
        $recentAttendance = AttendanceRecord::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->with('user')
            ->where('status', 'present')
            ->latest('attendance_date')
            ->limit(3)
            ->get();

        foreach ($recentAttendance as $attendance) {
            $activities->push([
                'type' => 'attendance',
                'title' => 'Training attendance',
                'description' => $attendance->user->first_name . ' attended session',
                'date' => $attendance->created_at,
                'icon' => 'fa-user-check',
                'color' => 'primary'
            ]);
        }

        // Recent leave requests
        $recentLeave = LeaveRequest::whereHas('user', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->with('user', 'leaveType')
            ->latest('created_at')
            ->limit(2)
            ->get();

        foreach ($recentLeave as $leave) {
            $activities->push([
                'type' => 'leave',
                'title' => 'Leave request',
                'description' => $leave->user->first_name . ' - ' . ($leave->leaveType->name ?? 'Leave'),
                'date' => $leave->created_at,
                'icon' => 'fa-calendar-times',
                'color' => 'warning'
            ]);
        }

        return $activities->sortByDesc('date')->take($limit)->values();
    }

    private function getUpcomingSchedules($user, $limit = 5)
    {
        return Schedule::whereHas('program.users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
            ->with('program')
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->limit($limit)
            ->get();
    }

    private function getComplianceMetrics($companyId, $currentMonth)
    {
        $startOfTaxYear = Carbon::create($currentMonth->year, 3, 1);
        if ($currentMonth->month < 3) {
            $startOfTaxYear->subYear();
        }

        $endOfTaxYear = $startOfTaxYear->copy()->addYear()->subDay();

        return [
            'ytd_paye' => Payslip::where('company_id', $companyId)
                ->whereBetween('pay_date', [$startOfTaxYear, $endOfTaxYear])
                ->sum('paye_tax'),

            'ytd_uif' => Payslip::where('company_id', $companyId)
                ->whereBetween('pay_date', [$startOfTaxYear, $endOfTaxYear])
                ->sum(DB::raw('uif_employee + uif_employer')),

            'ytd_eti' => Payslip::where('company_id', $companyId)
                ->whereBetween('pay_date', [$startOfTaxYear, $endOfTaxYear])
                ->sum('eti_benefit'),

            'pending_submissions' => $this->getPendingSubmissions($companyId),

            'tax_year' => $startOfTaxYear->year . '/' . $endOfTaxYear->year,
        ];
    }

    private function getPendingSubmissions($companyId)
    {
        $submissions = 0;

        // Check if EMP201 is overdue (due by 7th of following month)
        if (now()->day > 7) {
            $lastMonth = now()->subMonth();
            $submitted = Payslip::where('company_id', $companyId)
                ->whereYear('pay_date', $lastMonth->year)
                ->whereMonth('pay_date', $lastMonth->month)
                ->whereNotNull('exported_to_sars_at')
                ->exists();
            if (!$submitted) $submissions++;
        }

        return $submissions;
    }

    private function calculateBudgetUtilization($companyId)
    {
        $totalAllocated = ProgramBudget::where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('total_budget');

        $totalUsed = ProgramBudget::where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('used_budget');

        if ($totalAllocated > 0) {
            return round(($totalUsed / $totalAllocated) * 100, 1);
        }

        return 0;
    }
}