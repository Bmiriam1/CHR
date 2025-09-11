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
                        {{ $payslip->user->first_name }} {{ $payslip->user->last_name }} -
                        {{ $payslip->pay_period_formatted }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('payslips.download', $payslip) }}"
                        class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                        <i class="fa fa-download mr-2"></i>
                        Download PDF
                    </a>
                    <a href="{{ route('payslips.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Payslips
                    </a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                <!-- Main Payslip Content -->
                <div class="lg:col-span-2 space-y-4 sm:space-y-5">
                    <!-- Employee Information -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Employee Information
                            </h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <h3
                                        class="text-xs+ font-medium uppercase tracking-widest text-slate-400 dark:text-navy-300 mb-3">
                                        Personal Details
                                    </h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Name</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->user->first_name }} {{ $payslip->user->last_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Employee Code</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->user->employee_code }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">ID Number</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->user->id_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Email</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->user->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3
                                        class="text-xs+ font-medium uppercase tracking-widest text-slate-400 dark:text-navy-300 mb-3">
                                        Pay Period
                                    </h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Period</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->pay_period_formatted }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Pay Date</p>
                                            <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                                {{ $payslip->pay_date->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Status</p>
                                            <div class="badge space-x-2.5 px-3 py-1 mt-1
                                                    @if($payslip->status === 'draft') bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100
                                                    @elseif($payslip->status === 'generated') bg-info/10 text-info dark:bg-info-focus dark:text-info
                                                    @elseif($payslip->status === 'approved') bg-success/10 text-success dark:bg-success-focus dark:text-success-light
                                                    @elseif($payslip->status === 'paid') bg-secondary/10 text-secondary dark:bg-secondary-focus dark:text-secondary-light
                                                    @endif">
                                                <div class="h-2 w-2 rounded-full 
                                                        @if($payslip->status === 'draft') bg-slate-500
                                                        @elseif($payslip->status === 'generated') bg-info
                                                        @elseif($payslip->status === 'approved') bg-success
                                                        @elseif($payslip->status === 'paid') bg-secondary
                                                        @endif"></div>
                                                <span>{{ ucfirst($payslip->status) }}</span>
                                            </div>
                                        </div>
                                    </div>
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
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-slate-700 dark:text-navy-100">
                                        {{ $payslip->days_worked }}</div>
                                    <div class="text-xs+ text-slate-400 dark:text-navy-300">Total Days</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-success">{{ $payslip->days_present }}</div>
                                    <div class="text-xs+ text-slate-400 dark:text-navy-300">Present</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-warning">{{ $payslip->days_authorized_absent }}
                                    </div>
                                    <div class="text-xs+ text-slate-400 dark:text-navy-300">Authorized Absent</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-error">{{ $payslip->days_unauthorized_absent }}
                                    </div>
                                    <div class="text-xs+ text-slate-400 dark:text-navy-300">Unauthorized Absent</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pay Breakdown -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Pay Breakdown
                            </h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-4">
                                <!-- Earnings -->
                                <div>
                                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-3">Earnings</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-400 dark:text-navy-300">Basic Pay
                                                ({{ $payslip->days_present + $payslip->days_authorized_absent }} days ×
                                                R{{ number_format($payslip->daily_rate, 2) }})</span>
                                            <span
                                                class="text-sm font-medium text-slate-700 dark:text-navy-100">R{{ number_format($payslip->basic_pay, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-400 dark:text-navy-300">Transport
                                                Allowance</span>
                                            <span
                                                class="text-sm font-medium text-slate-700 dark:text-navy-100">R{{ number_format($payslip->transport_allowance, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-400 dark:text-navy-300">Meal Allowance</span>
                                            <span
                                                class="text-sm font-medium text-slate-700 dark:text-navy-100">R{{ number_format($payslip->meal_allowance, 2) }}</span>
                                        </div>
                                        @if($payslip->accommodation_allowance > 0)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-400 dark:text-navy-300">Accommodation
                                                    Allowance</span>
                                                <span
                                                    class="text-sm font-medium text-slate-700 dark:text-navy-100">R{{ number_format($payslip->accommodation_allowance, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($payslip->other_allowance > 0)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-400 dark:text-navy-300">Other Allowance</span>
                                                <span
                                                    class="text-sm font-medium text-slate-700 dark:text-navy-100">R{{ number_format($payslip->other_allowance, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="border-t border-slate-200 dark:border-navy-500 pt-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Gross
                                                    Pay</span>
                                                <span
                                                    class="text-sm font-bold text-slate-700 dark:text-navy-100">R{{ number_format($payslip->gross_pay, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deductions -->
                                <div>
                                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-3">Deductions</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-400 dark:text-navy-300">UIF (1%)</span>
                                            <span
                                                class="text-sm font-medium text-slate-700 dark:text-navy-100">-R{{ number_format($payslip->uif_deduction, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-400 dark:text-navy-300">PAYE</span>
                                            <span
                                                class="text-sm font-medium text-slate-700 dark:text-navy-100">-R{{ number_format($payslip->paye_deduction, 2) }}</span>
                                        </div>
                                        @if($payslip->other_deductions > 0)
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-400 dark:text-navy-300">Other Deductions</span>
                                                <span
                                                    class="text-sm font-medium text-slate-700 dark:text-navy-100">-R{{ number_format($payslip->other_deductions, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="border-t border-slate-200 dark:border-navy-500 pt-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Total
                                                    Deductions</span>
                                                <span
                                                    class="text-sm font-bold text-slate-700 dark:text-navy-100">-R{{ number_format($payslip->total_deductions, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Pay -->
                                <div class="border-t border-slate-200 dark:border-navy-500 pt-4">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-bold text-slate-700 dark:text-navy-100">Net Pay</span>
                                        <span
                                            class="text-lg font-bold text-success">R{{ number_format($payslip->net_pay, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4 sm:space-y-5">
                    <!-- Actions -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Actions
                            </h3>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                <a href="{{ route('payslips.download', $payslip) }}"
                                    class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                    <i class="fa fa-download mr-2"></i>
                                    Download PDF
                                </a>

                                @if($payslip->status === 'generated')
                                    <form action="{{ route('payslips.approve', $payslip) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn w-full bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                                            <i class="fa fa-check mr-2"></i>
                                            Approve Payslip
                                        </button>
                                    </form>
                                @endif

                                @if($payslip->status === 'approved')
                                    <form action="{{ route('payslips.mark-paid', $payslip) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn w-full bg-secondary font-medium text-white hover:bg-secondary-focus focus:bg-secondary-focus active:bg-secondary-focus/90">
                                            <i class="fa fa-coins mr-2"></i>
                                            Mark as Paid
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tax Information -->
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Tax Information
                            </h3>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Taxable Income</p>
                                    <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->taxable_income, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Annual Taxable Income</p>
                                    <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->annual_taxable_income, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">UIF Contribution</p>
                                    <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                        R{{ number_format($payslip->uif_contribution, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Program Information -->
                    @if($payslip->program)
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Program Information
                                </h3>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Program</p>
                                        <p class="text-sm+ text-slate-700 dark:text-navy-100">{{ $payslip->program->title }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Program Code</p>
                                        <p class="text-sm+ text-slate-700 dark:text-navy-100">
                                            {{ $payslip->program->program_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
