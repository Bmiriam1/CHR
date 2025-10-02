<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build query for user's payslips
        $query = Payslip::where('user_id', $user->id);
        
        // Filter by year if provided
        if ($request->filled('year')) {
            $query->whereYear('pay_period_start', $request->year);
        }
        
        // Filter by month if provided
        if ($request->filled('month')) {
            $query->whereMonth('pay_period_start', $request->month);
        }
        
        // Search by description or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $payslips = $query->orderBy('pay_period_start', 'desc')
                         ->paginate(12)
                         ->appends($request->all());
        
        // Get available years for filter dropdown
        $availableYears = Payslip::where('user_id', $user->id)
                                 ->selectRaw('DISTINCT YEAR(pay_period_start) as year')
                                 ->orderBy('year', 'desc')
                                 ->pluck('year');
        
        // Calculate summary stats
        $stats = [
            'total_payslips' => Payslip::where('user_id', $user->id)->count(),
            'ytd_gross' => Payslip::where('user_id', $user->id)
                                 ->whereYear('pay_period_start', now()->year)
                                 ->sum('gross_pay'),
            'ytd_net' => Payslip::where('user_id', $user->id)
                               ->whereYear('pay_period_start', now()->year)
                               ->sum('net_pay'),
            'ytd_deductions' => Payslip::where('user_id', $user->id)
                                      ->whereYear('pay_period_start', now()->year)
                                      ->sum('total_deductions'),
        ];
        
        return view('payslips.index', compact('payslips', 'availableYears', 'stats'));
    }
    
    public function show(Payslip $payslip)
    {
        // Check if user owns this payslip
        if ($payslip->user_id !== Auth::id()) {
            return redirect()->route('payslips.index')
                ->with('error', 'You do not have permission to view this payslip.');
        }
        
        return view('payslips.show', compact('payslip'));
    }
    
    public function downloadPdf(Payslip $payslip)
    {
        // Check if user owns this payslip
        if ($payslip->user_id !== Auth::id()) {
            return redirect()->route('payslips.index')
                ->with('error', 'You do not have permission to download this payslip.');
        }
        
        $pdf = Pdf::loadView('payslips.pdf', compact('payslip'));
        
        $filename = 'payslip_' . $payslip->reference_number . '_' . $payslip->pay_period_start->format('Y-m') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function downloadCsv(Request $request)
    {
        $user = Auth::user();
        
        // Get payslips based on filters
        $query = Payslip::where('user_id', $user->id);
        
        if ($request->filled('year')) {
            $query->whereYear('pay_period_start', $request->year);
        }
        
        if ($request->filled('month')) {
            $query->whereMonth('pay_period_start', $request->month);
        }
        
        $payslips = $query->orderBy('pay_period_start', 'desc')->get();
        
        $filename = 'payslips_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($payslips) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Reference Number',
                'Pay Period Start',
                'Pay Period End',
                'Payment Date',
                'Gross Pay',
                'PAYE',
                'UIF',
                'Other Deductions',
                'Total Deductions',
                'Net Pay',
                'Description'
            ]);
            
            // Add data rows
            foreach ($payslips as $payslip) {
                fputcsv($file, [
                    $payslip->reference_number,
                    $payslip->pay_period_start->format('Y-m-d'),
                    $payslip->pay_period_end->format('Y-m-d'),
                    $payslip->payment_date->format('Y-m-d'),
                    number_format($payslip->gross_pay, 2, '.', ''),
                    number_format($payslip->paye, 2, '.', ''),
                    number_format($payslip->uif, 2, '.', ''),
                    number_format($payslip->other_deductions, 2, '.', ''),
                    number_format($payslip->total_deductions, 2, '.', ''),
                    number_format($payslip->net_pay, 2, '.', ''),
                    $payslip->description ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}