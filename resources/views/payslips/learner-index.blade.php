@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        My Earnings Report
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        View and download your stipend history and payslips
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Total Earnings -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Paid Earnings</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                R{{ number_format($stats['total_earnings'], 2) }}
                            </h3>
                            <p class="text-xs text-success">All Time</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-coins text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Payslips -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Payslips</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['total_payslips']) }}
                            </h3>
                            <p class="text-xs text-info">All Time</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-file-invoice text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- YTD Earnings -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD Earnings</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                R{{ number_format($stats['ytd_earnings'], 2) }}
                            </h3>
                            <p class="text-xs text-secondary">{{ now()->year }}</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                            <i class="fa fa-calendar text-secondary"></i>
                        </div>
                    </div>
                </div>

                <!-- YTD Tax -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD Tax Deducted</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                R{{ number_format($stats['ytd_tax'], 2) }}
                            </h3>
                            <p class="text-xs text-warning">{{ now()->year }}</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-percent text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mt-6">
                <div class="px-4 py-4 sm:px-5">
                    <form method="GET" action="{{ route('payslips.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100">
                                From Date
                            </label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100">
                                To Date
                            </label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100">
                                Program
                            </label>
                            <select name="program_id"
                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2">
                                <option value="">All Programs</option>
                                @foreach(auth()->user()->programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus w-full">
                                <i class="fa fa-filter mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payslips Table -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            My Payslips
                        </h2>
                    </div>

                    @if($payslips->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="is-hoverable w-full text-left">
                                <thead>
                                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Pay Period
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Program
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Days Worked
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Gross Pay
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Deductions
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Net Pay
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Status
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payslips as $payslip)
                                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                <div>
                                                    <p class="font-medium">{{ $payslip->payroll_period_start->format('d M Y') }}</p>
                                                    <p class="text-xs text-slate-400">to {{ $payslip->payroll_period_end->format('d M Y') }}</p>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                {{ $payslip->program ? $payslip->program->title : 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                {{ $payslip->days_worked }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                R{{ number_format($payslip->gross_earnings, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                R{{ number_format($payslip->total_deductions, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 font-medium text-success sm:px-5">
                                                R{{ number_format($payslip->net_pay, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                <div class="badge space-x-2.5 px-3 py-1
                                                    @if($payslip->status === 'draft') bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100
                                                    @elseif($payslip->status === 'calculated') bg-info/10 text-info dark:bg-info-focus dark:text-info
                                                    @elseif($payslip->status === 'approved') bg-success/10 text-success dark:bg-success-focus dark:text-success-light
                                                    @elseif($payslip->status === 'paid') bg-secondary/10 text-secondary dark:bg-secondary-focus dark:text-secondary-light
                                                    @endif">
                                                    <span>{{ ucfirst($payslip->status) }}</span>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('payslips.show', $payslip) }}"
                                                        class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                                        title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'pdf']) }}"
                                                        class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                                        title="Download PDF">
                                                        <i class="fa fa-file-pdf text-error"></i>
                                                    </a>
                                                    <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'csv']) }}"
                                                        class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                                        title="Download CSV">
                                                        <i class="fa fa-file-csv text-success"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-4 py-4 sm:px-5">
                            {{ $payslips->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="px-4 py-12 text-center sm:px-5">
                            <div class="text-slate-400 dark:text-navy-300 mb-3">
                                <i class="fa fa-file-invoice text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                No payslips found
                            </h3>
                            <p class="text-slate-500 dark:text-navy-300 mt-1">
                                You don't have any payslips yet. They will appear here once generated by your administrator.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection