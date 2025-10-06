@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Payslip Details
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        {{ $payslip->payroll_period_start->format('d M Y') }} - {{ $payslip->payroll_period_end->format('d M Y') }}
                    </p>
                </div>
                <a href="{{ route('payslips.index') }}"
                    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                    <i class="fa fa-arrow-left mr-2"></i>
                    Back to Payslips
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-5">
                    <!-- Pay Period Information -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Pay Period Information
                            </h2>
                            <div class="badge space-x-2.5 px-3 py-1
                                @if($payslip->status === 'draft') bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100
                                @elseif($payslip->status === 'calculated') bg-info/10 text-info dark:bg-info-focus dark:text-info
                                @elseif($payslip->status === 'approved') bg-success/10 text-success dark:bg-success-focus dark:text-success-light
                                @elseif($payslip->status === 'paid') bg-secondary/10 text-secondary dark:bg-secondary-focus dark:text-secondary-light
                                @endif">
                                <span>{{ ucfirst($payslip->status) }}</span>
                            </div>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Pay Period</p>
                                    <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                        {{ $payslip->payroll_period_start->format('d M Y') }} - {{ $payslip->payroll_period_end->format('d M Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Pay Date</p>
                                    <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                        {{ $payslip->pay_date->format('d M Y') }}
                                    </p>
                                </div>
                                @if($payslip->program)
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Program</p>
                                        <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                            {{ $payslip->program->title }}
                                        </p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Daily Rate</p>
                                    <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->daily_rate_used, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Summary -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Attendance Summary
                            </h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-lg bg-slate-50 p-4 dark:bg-navy-600">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-slate-700 dark:text-navy-100">
                                            {{ $payslip->days_worked }}
                                        </div>
                                        <div class="text-xs+ text-slate-400 dark:text-navy-300 mt-1">Total Days</div>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-success/10 p-4">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-success">
                                            {{ $payslip->days_worked - ($payslip->days_on_leave ?? 0) - ($payslip->days_absent ?? 0) }}
                                        </div>
                                        <div class="text-xs+ text-success mt-1">Days Paid</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Earnings Breakdown
                            </h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                    <span class="text-sm text-slate-600 dark:text-navy-200">Basic Stipend</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->basic_earnings, 2) }}
                                    </span>
                                </div>
                                @if($payslip->transport_allowance > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Transport Allowance</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                            R{{ number_format($payslip->transport_allowance, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if($payslip->meal_allowance > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Meal Allowance</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                            R{{ number_format($payslip->meal_allowance, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if($payslip->accommodation_allowance > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Accommodation Allowance</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                            R{{ number_format($payslip->accommodation_allowance, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if($payslip->other_allowances > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Other Allowance</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                            R{{ number_format($payslip->other_allowances, 2) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center py-3 bg-slate-50 dark:bg-navy-600 rounded-lg px-3">
                                    <span class="text-base font-semibold text-slate-700 dark:text-navy-100">Gross Earnings</span>
                                    <span class="text-base font-bold text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->gross_earnings, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Breakdown -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Deductions
                            </h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                @if($payslip->uif_employee > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <div>
                                            <span class="text-sm text-slate-600 dark:text-navy-200">UIF Contribution</span>
                                            <p class="text-xs text-slate-400 dark:text-navy-300">1% of gross earnings</p>
                                        </div>
                                        <span class="text-sm font-semibold text-error">
                                            -R{{ number_format($payslip->uif_employee, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if($payslip->paye_tax > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <div>
                                            <span class="text-sm text-slate-600 dark:text-navy-200">PAYE Tax</span>
                                            <p class="text-xs text-slate-400 dark:text-navy-300">Income tax deduction</p>
                                        </div>
                                        <span class="text-sm font-semibold text-error">
                                            -R{{ number_format($payslip->paye_tax, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if($payslip->other_deductions > 0)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-150 dark:border-navy-500">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Other Deductions</span>
                                        <span class="text-sm font-semibold text-error">
                                            -R{{ number_format($payslip->other_deductions, 2) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center py-3 bg-error/10 rounded-lg px-3">
                                    <span class="text-base font-semibold text-slate-700 dark:text-navy-100">Total Deductions</span>
                                    <span class="text-base font-bold text-error">
                                        -R{{ number_format($payslip->total_deductions, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Pay -->
                    <div class="card bg-gradient-to-r from-success to-success-focus">
                        <div class="px-4 py-6 sm:px-5">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-white/80 mb-1">Net Pay</p>
                                    <h2 class="text-3xl font-bold text-white">
                                        R{{ number_format($payslip->net_pay, 2) }}
                                    </h2>
                                    <p class="text-xs text-white/70 mt-2">Amount to be paid</p>
                                </div>
                                <div class="text-white/20">
                                    <i class="fa fa-coins text-6xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4 sm:space-y-5">
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Download Options
                            </h3>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'pdf']) }}"
                                    class="btn w-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                                    <i class="fa fa-file-pdf mr-2"></i>
                                    Download as PDF
                                </a>
                                <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'csv']) }}"
                                    class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                    <i class="fa fa-file-csv mr-2"></i>
                                    Download as CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Year-to-Date Summary -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Year-to-Date ({{ now()->year }})
                            </h3>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD Gross Earnings</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->ytd_gross_earnings ?? $payslip->gross_earnings, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD PAYE Tax</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->ytd_paye_tax ?? $payslip->paye_tax, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD UIF</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->ytd_uif_employee ?? $payslip->uif_employee, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help & Support -->
                    <div class="card bg-info/10">
                        <div class="px-4 py-4 sm:px-5">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <i class="fa fa-info-circle text-2xl text-info"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-700 dark:text-navy-100 mb-1">
                                        Need Help?
                                    </h4>
                                    <p class="text-xs text-slate-600 dark:text-navy-200">
                                        If you have questions about your payslip or notice any discrepancies, please contact your HR administrator.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection