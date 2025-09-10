@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        SARS Compliance Dashboard
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Tax Year {{ $taxYear }}/{{ $taxYear + 1 }} - Compliance Overview
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 dark:text-navy-300">
                        Last Updated: {{ now()->format('l, F j, Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Total Companies -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Companies</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['total_companies']) }}</h3>
                            <p class="text-xs text-info">Registered</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-building text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Programs -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Active Programs</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['active_programs']) }}</h3>
                            <p class="text-xs text-success">Running</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-graduation-cap text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Learners -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Learners</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['total_learners']) }}</h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300">With Attendance</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                            <i class="fa fa-users text-secondary"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Submissions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Pending Submissions</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['pending_submissions'] }}</h3>
                            <p class="text-xs {{ $stats['pending_submissions'] > 0 ? 'text-warning' : 'text-success' }}">
                                {{ $stats['pending_submissions'] > 0 ? 'Action Required' : 'Up to Date' }}
                            </p>
                        </div>
                        <div
                            class="mask is-squircle flex size-10 items-center justify-center {{ $stats['pending_submissions'] > 0 ? 'bg-warning/10' : 'bg-success/10' }}">
                            <i
                                class="fa {{ $stats['pending_submissions'] > 0 ? 'fa-exclamation-triangle text-warning' : 'fa-check text-success' }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Overview -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Monthly PAYE -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Monthly PAYE</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['monthly_paye'], 2) }}</h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300">{{ now()->format('F Y') }}</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-error/10">
                            <i class="fa fa-coins text-error"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly UIF -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Monthly UIF</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['monthly_uif'], 2) }}</h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Employee + Employer</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-shield-alt text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- ETI Benefits -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">ETI Benefits</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['monthly_eti_benefit'], 2) }}</h3>
                            <p class="text-xs text-success">Monthly Claim</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-trophy text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Payslips -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Monthly Payslips</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['monthly_payslips']) }}</h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Generated</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-file-invoice text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Quick Actions
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @can('generate sars reports')
                                <a href="{{ route('compliance.sars.emp201.form') }}"
                                    class="flex items-center justify-center rounded-lg bg-primary/10 p-4 transition-colors hover:bg-primary/20">
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fa fa-file-alt text-2xl text-primary"></i>
                                        </div>
                                        <p class="text-sm font-medium text-primary">Generate EMP201</p>
                                        <p class="text-xs text-slate-400">Monthly PAYE Return</p>
                                    </div>
                                </a>
                            @endcan

                            @can('generate sars reports')
                                <a href="{{ route('compliance.sars.emp501.form') }}"
                                    class="flex items-center justify-center rounded-lg bg-success/10 p-4 transition-colors hover:bg-success/20">
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fa fa-chart-bar text-2xl text-success"></i>
                                        </div>
                                        <p class="text-sm font-medium text-success">Generate EMP501</p>
                                        <p class="text-xs text-slate-400">Annual Reconciliation</p>
                                    </div>
                                </a>
                            @endcan

                            @can('generate tax certificates')
                                <a href="{{ route('compliance.tax_certificates.index') }}"
                                    class="flex items-center justify-center rounded-lg bg-warning/10 p-4 transition-colors hover:bg-warning/20">
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fa fa-certificate text-2xl text-warning"></i>
                                        </div>
                                        <p class="text-sm font-medium text-warning">Tax Certificates</p>
                                        <p class="text-xs text-slate-400">IRP5 & IT3(a)</p>
                                    </div>
                                </a>
                            @endcan

                            @can('generate eti claims')
                                <a href="{{ route('compliance.eti.dashboard') }}"
                                    class="flex items-center justify-center rounded-lg bg-info/10 p-4 transition-colors hover:bg-info/20">
                                    <div class="text-center">
                                        <div class="mb-2">
                                            <i class="fa fa-trophy text-2xl text-info"></i>
                                        </div>
                                        <p class="text-sm font-medium text-info">ETI Claims</p>
                                        <p class="text-xs text-slate-400">Employment Tax Incentive</p>
                                    </div>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            @if(count($alerts) > 0)
                <!-- Compliance Alerts -->
                <div class="mt-6">
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Compliance Alerts
                            </h2>
                            <span class="badge bg-warning text-white">{{ count($alerts) }}</span>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3">
                                @foreach($alerts as $alert)
                                    <div class="flex items-center space-x-3 rounded-lg bg-warning/10 p-3">
                                        <div class="flex size-8 items-center justify-center rounded-full bg-warning text-white">
                                            <i class="fa fa-exclamation-triangle text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $alert['title'] }}
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                {{ $alert['message'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <a href="{{ $alert['action_url'] }}"
                                                class="btn size-8 rounded-full bg-warning p-0 font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                                                <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Submissions -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Recent Submissions
                        </h2>
                        @can('run compliance checks')
                            <a href="{{ route('compliance.checks.index') }}"
                                class="border-b border-dotted border-current pb-0.5 text-xs+ font-medium text-primary outline-none transition-colors duration-300 hover:text-primary/70 focus:text-primary/70">
                                View All Checks
                            </a>
                        @endcan
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        @if(count($recentSubmissions) > 0)
                            <div class="space-y-3">
                                @foreach($recentSubmissions as $submission)
                                    <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                        <div class="flex size-8 items-center justify-center rounded-full bg-success/10">
                                            <i class="fa fa-check text-success text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $submission['type'] }} - {{ $submission['period'] }}
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                Submitted {{ \Carbon\Carbon::parse($submission['submitted_at'])->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge bg-success text-white">{{ ucfirst($submission['status']) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-slate-400 dark:text-navy-300 mb-2">
                                    <i class="fa fa-file-alt text-4xl"></i>
                                </div>
                                <p class="text-slate-500 dark:text-navy-400">No recent submissions</p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Submissions will appear here once you
                                    start generating reports</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection