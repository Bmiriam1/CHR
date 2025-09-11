<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayslipController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return view('payslips.index', [
                'payslips' => collect(),
                'message' => 'Please contact your administrator to assign you to a company.'
            ]);
        }

        $payslips = Payslip::with(['user', 'program'])
            ->where('company_id', $user->company_id)
            ->orderBy('pay_period_start', 'desc')
            ->paginate(15);

        $stats = [
            'total_payslips' => Payslip::where('company_id', $user->company_id)->count(),
            'draft_payslips' => Payslip::where('company_id', $user->company_id)->where('status', 'draft')->count(),
            'generated_payslips' => Payslip::where('company_id', $user->company_id)->where('status', 'generated')->count(),
            'paid_payslips' => Payslip::where('company_id', $user->company_id)->where('status', 'paid')->count(),
        ];

        return view('payslips.index', compact('payslips', 'stats'));
    }

    public function show(Payslip $payslip)
    {
        $payslip->load(['user', 'program', 'createdBy', 'approvedBy']);
        return view('payslips.show', compact('payslip'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'pay_date' => 'required|date|after_or_equal:pay_period_end',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->pay_period_start);
        $endDate = Carbon::parse($request->pay_period_end);
        $payDate = Carbon::parse($request->pay_date);

        // Get all learners for the company
        $learners = User::where('company_id', $user->company_id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'learner');
            })
            ->get();

        $generatedCount = 0;

        foreach ($learners as $learner) {
            // Check if payslip already exists for this period
            $existingPayslip = Payslip::where('user_id', $learner->id)
                ->where('pay_period_start', $startDate)
                ->where('pay_period_end', $endDate)
                ->first();

            if ($existingPayslip) {
                continue; // Skip if already exists
            }

            // Get attendance records for the period
            $attendanceRecords = AttendanceRecord::where('user_id', $learner->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            // Calculate attendance statistics
            $totalDays = $attendanceRecords->count();
            $presentDays = $attendanceRecords->where('status', 'present')->count();
            $absentDays = $attendanceRecords->where('status', 'absent_unauthorized')->count();
            $authorizedAbsentDays = $attendanceRecords->where('status', 'absent_authorized')->count();

            // Get program details for daily rate
            $program = $learner->programs()->first();
            $dailyRate = $program ? $program->daily_rate : 150.00; // Default rate

            // Calculate basic pay (present days + authorized absent days)
            $paidDays = $presentDays + $authorizedAbsentDays;
            $basicPay = $paidDays * $dailyRate;

            // Calculate allowances (simplified)
            $transportAllowance = $paidDays * 50.00; // R50 per day
            $mealAllowance = $paidDays * 30.00; // R30 per day

            // Calculate gross pay
            $grossPay = $basicPay + $transportAllowance + $mealAllowance;

            // Calculate annual taxable income (simplified)
            $annualTaxableIncome = $grossPay * 12;

            // Create payslip
            $payslip = Payslip::create([
                'user_id' => $learner->id,
                'company_id' => $user->company_id,
                'program_id' => $program ? $program->id : null,
                'pay_period_start' => $startDate,
                'pay_period_end' => $endDate,
                'pay_date' => $payDate,
                'basic_pay' => $basicPay,
                'daily_rate' => $dailyRate,
                'days_worked' => $totalDays,
                'days_present' => $presentDays,
                'days_absent' => $absentDays,
                'days_authorized_absent' => $authorizedAbsentDays,
                'days_unauthorized_absent' => $absentDays,
                'transport_allowance' => $transportAllowance,
                'meal_allowance' => $mealAllowance,
                'gross_pay' => $grossPay,
                'annual_taxable_income' => $annualTaxableIncome,
                'status' => 'generated',
                'created_by' => $user->id,
            ]);

            // Calculate UIF and PAYE
            $payslip->calculateUIF();
            $payslip->calculatePAYE();
            $payslip->calculateGrossPay();
            $payslip->calculateNetPay();
            $payslip->save();

            $generatedCount++;
        }

        return redirect()->route('payslips.index')
            ->with('success', "Successfully generated {$generatedCount} payslips for the period {$startDate->format('d M Y')} to {$endDate->format('d M Y')}.");
    }

    public function generateForm()
    {
        return view('payslips.generate');
    }

    public function download(Payslip $payslip)
    {
        // This would generate a PDF payslip
        // For now, just return the view
        $payslip->load(['user', 'program']);
        return view('payslips.download', compact('payslip'));
    }

    public function approve(Payslip $payslip)
    {
        $payslip->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payslip approved successfully.');
    }

    public function markAsPaid(Payslip $payslip)
    {
        $payslip->update([
            'status' => 'paid',
        ]);

        return redirect()->back()->with('success', 'Payslip marked as paid.');
    }
}
