@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Payslips Management
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Manage employee payslips and payroll processing
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('payslips.generate') }}"
                        class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Generate Payslips
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
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

                <!-- Draft Payslips -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Draft Payslips</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['draft_payslips']) }}
                            </h3>
                            <p class="text-xs text-warning">Pending</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Generated Payslips -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Generated Payslips</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['generated_payslips']) }}
                            </h3>
                            <p class="text-xs text-success">Ready</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Paid Payslips -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Paid Payslips</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['paid_payslips']) }}
                            </h3>
                            <p class="text-xs text-secondary">Completed</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                            <i class="fa fa-coins text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payslips Table -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Recent Payslips
                        </h2>
                    </div>

                    @if($payslips->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="is-hoverable w-full text-left">
                                <thead>
                                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Employee
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Pay Period
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Days Worked
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Gross Pay
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Net Pay
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Status
                                        </th>
                                        <th
                                            class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payslips as $payslip)
                                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                <div class="flex items-center space-x-3">
                                                    <div class="avatar h-9 w-9">
                                                        <div class="is-initial rounded-full bg-info text-white">
                                                            {{ substr($payslip->user->first_name, 0, 1) }}{{ substr($payslip->user->last_name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                                            {{ $payslip->user->first_name }} {{ $payslip->user->last_name }}
                                                        </p>
                                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                                            {{ $payslip->user->employee_code }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                {{ $payslip->pay_period_formatted }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                {{ $payslip->days_present }}/{{ $payslip->days_worked }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                R{{ number_format($payslip->gross_pay, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 dark:text-navy-100 sm:px-5">
                                                R{{ number_format($payslip->net_pay, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                <div class="badge space-x-2.5 px-3 py-1
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
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('payslips.show', $payslip) }}"
                                                        class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('payslips.download', $payslip) }}"
                                                        class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @if($payslip->status === 'generated')
                                                        <form action="{{ route('payslips.approve', $payslip) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                <i class="fa fa-check text-success"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($payslip->status === 'approved')
                                                        <form action="{{ route('payslips.mark-paid', $payslip) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                <i class="fa fa-coins text-secondary"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-4 py-4 sm:px-5">
                            {{ $payslips->links() }}
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
                                Get started by generating payslips for your employees.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('payslips.generate') }}"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <i class="fa fa-plus mr-2"></i>
                                    Generate Payslips
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection