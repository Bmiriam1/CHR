<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $dateRange = $request->get('date_range', '30'); // days
        $programId = $request->get('program_id');

        $startDate = now()->subDays($dateRange);
        $endDate = now();

        // Overall attendance statistics
        $stats = $this->getAttendanceStats($companyId, $startDate, $endDate, $programId);

        // Program-wise statistics
        $programStats = $this->getProgramStats($companyId, $startDate, $endDate);

        // Daily attendance trends
        $dailyTrends = $this->getDailyTrends($companyId, $startDate, $endDate, $programId);

        // Top performers
        $topPerformers = $this->getTopPerformers($companyId, $startDate, $endDate, $programId);

        // Proof approval statistics
        $proofStats = $this->getProofStats($companyId, $startDate, $endDate);

        // Programs for filter
        $programs = Program::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        return view('analytics.index', compact(
            'stats',
            'programStats',
            'dailyTrends',
            'topPerformers',
            'proofStats',
            'programs',
            'dateRange',
            'programId'
        ));
    }

    /**
     * Get overall attendance statistics.
     */
    private function getAttendanceStats($companyId, $startDate, $endDate, $programId = null)
    {
        $query = AttendanceRecord::where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($programId) {
            $query->where('program_id', $programId);
        }

        $totalRecords = $query->count();

        if ($totalRecords === 0) {
            return [
                'total_records' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'attendance_rate' => 0,
                'authorized_absent_rate' => 0,
                'unauthorized_absent_rate' => 0,
            ];
        }

        $statusCounts = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $presentCount = ($statusCounts['present'] ?? 0) + ($statusCounts['late'] ?? 0);
        $absentCount = ($statusCounts['absent_unauthorized'] ?? 0) + ($statusCounts['absent_authorized'] ?? 0);
        $authorizedAbsentCount = $statusCounts['absent_authorized'] ?? 0;
        $unauthorizedAbsentCount = $statusCounts['absent_unauthorized'] ?? 0;

        return [
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $statusCounts['late'] ?? 0,
            'attendance_rate' => round(($presentCount / $totalRecords) * 100, 2),
            'authorized_absent_rate' => round(($authorizedAbsentCount / $totalRecords) * 100, 2),
            'unauthorized_absent_rate' => round(($unauthorizedAbsentCount / $totalRecords) * 100, 2),
        ];
    }

    /**
     * Get program-wise statistics.
     */
    private function getProgramStats($companyId, $startDate, $endDate)
    {
        return Program::where('company_id', $companyId)
            ->withCount([
                'attendanceRecords as total_attendance' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                },
                'attendanceRecords as present_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate])
                        ->whereIn('status', ['present', 'late']);
                },
                'attendanceRecords as absent_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate])
                        ->whereIn('status', ['absent_unauthorized', 'absent_authorized']);
                }
            ])
            ->get()
            ->map(function ($program) {
                $attendanceRate = $program->total_attendance > 0
                    ? round(($program->present_count / $program->total_attendance) * 100, 2)
                    : 0;

                return [
                    'program' => $program,
                    'total_attendance' => $program->total_attendance,
                    'present_count' => $program->present_count,
                    'absent_count' => $program->absent_count,
                    'attendance_rate' => $attendanceRate,
                ];
            });
    }

    /**
     * Get daily attendance trends.
     */
    private function getDailyTrends($companyId, $startDate, $endDate, $programId = null)
    {
        $query = AttendanceRecord::where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($programId) {
            $query->where('program_id', $programId);
        }

        return $query->select(
            'date',
            DB::raw('count(*) as total'),
            DB::raw('sum(case when status in ("present", "late") then 1 else 0 end) as present'),
            DB::raw('sum(case when status = "absent_unauthorized" then 1 else 0 end) as absent_unauthorized'),
            DB::raw('sum(case when status = "absent_authorized" then 1 else 0 end) as absent_authorized')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($record) {
                $attendanceRate = $record->total > 0
                    ? round(($record->present / $record->total) * 100, 2)
                    : 0;

                return [
                    'date' => $record->date->format('Y-m-d'),
                    'total' => $record->total,
                    'present' => $record->present,
                    'absent_unauthorized' => $record->absent_unauthorized,
                    'absent_authorized' => $record->absent_authorized,
                    'attendance_rate' => $attendanceRate,
                ];
            });
    }

    /**
     * Get top performing learners.
     */
    private function getTopPerformers($companyId, $startDate, $endDate, $programId = null)
    {
        $query = User::where('company_id', $companyId)
            ->where('role', 'learner')
            ->withCount([
                'attendanceRecords as total_attendance' => function ($query) use ($startDate, $endDate, $programId) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                    if ($programId) {
                        $query->where('program_id', $programId);
                    }
                },
                'attendanceRecords as present_count' => function ($query) use ($startDate, $endDate, $programId) {
                    $query->whereBetween('date', [$startDate, $endDate])
                        ->whereIn('status', ['present', 'late']);
                    if ($programId) {
                        $query->where('program_id', $programId);
                    }
                }
            ])
            ->having('total_attendance', '>', 0)
            ->orderByDesc('present_count')
            ->limit(10);

        return $query->get()->map(function ($learner) {
            $attendanceRate = $learner->total_attendance > 0
                ? round(($learner->present_count / $learner->total_attendance) * 100, 2)
                : 0;

            return [
                'learner' => $learner,
                'total_attendance' => $learner->total_attendance,
                'present_count' => $learner->present_count,
                'attendance_rate' => $attendanceRate,
            ];
        });
    }

    /**
     * Get proof approval statistics.
     */
    private function getProofStats($companyId, $startDate, $endDate)
    {
        $query = AttendanceRecord::where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('proof_document_path');

        $totalProofs = $query->count();
        $approvedProofs = $query->where('proof_status', 'approved')->count();
        $rejectedProofs = $query->where('proof_status', 'rejected')->count();
        $pendingProofs = $query->where('proof_status', 'pending')->count();

        return [
            'total_proofs' => $totalProofs,
            'approved_proofs' => $approvedProofs,
            'rejected_proofs' => $rejectedProofs,
            'pending_proofs' => $pendingProofs,
            'approval_rate' => $totalProofs > 0 ? round(($approvedProofs / $totalProofs) * 100, 2) : 0,
        ];
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $dateRange = $request->get('date_range', '30');
        $programId = $request->get('program_id');

        $startDate = now()->subDays($dateRange);
        $endDate = now();

        $stats = $this->getAttendanceStats($companyId, $startDate, $endDate, $programId);
        $programStats = $this->getProgramStats($companyId, $startDate, $endDate);
        $dailyTrends = $this->getDailyTrends($companyId, $startDate, $endDate, $programId);

        // Generate CSV content
        $csv = "Attendance Analytics Report\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $csv .= "Date Range: " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d') . "\n\n";

        $csv .= "Overall Statistics\n";
        $csv .= "Total Records," . $stats['total_records'] . "\n";
        $csv .= "Present Count," . $stats['present_count'] . "\n";
        $csv .= "Absent Count," . $stats['absent_count'] . "\n";
        $csv .= "Attendance Rate," . $stats['attendance_rate'] . "%\n\n";

        $csv .= "Program Statistics\n";
        $csv .= "Program,Total Attendance,Present Count,Absent Count,Attendance Rate\n";
        foreach ($programStats as $stat) {
            $csv .= $stat['program']->title . "," . $stat['total_attendance'] . "," . $stat['present_count'] . "," . $stat['absent_count'] . "," . $stat['attendance_rate'] . "%\n";
        }

        $filename = 'attendance_analytics_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
