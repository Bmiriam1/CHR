@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
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
        </div>

        <!-- Stats Cards -->
        <div class="col-span-12 lg:col-span-8">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-5">
                <!-- Total Companies -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['total_companies']) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Companies</p>
                </div>

                <!-- Active Programs -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['active_programs']) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Programs</p>
                </div>

                <!-- Total Learners -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['total_learners']) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Learners</p>
                </div>

                <!-- Pending Submissions -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $stats['pending_submissions'] }}
                        </p>
                        @if($stats['pending_submissions'] > 0)
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.888-.833-2.664 0L4.15 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>
                    <p class="mt-1 text-xs-plus">Pending</p>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="col-span-12 lg:col-span-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5">
                <!-- Monthly PAYE -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            R{{ number_format($stats['monthly_paye'], 0) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">PAYE Tax</p>
                </div>

                <!-- Monthly UIF -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            R{{ number_format($stats['monthly_uif'], 0) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">UIF</p>
                </div>

                <!-- ETI Benefits -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            R{{ number_format($stats['monthly_eti_benefit'], 0) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">ETI Benefits</p>
                </div>

                <!-- Monthly Payslips -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['monthly_payslips']) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Payslips</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-span-12 lg:col-span-8">
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
        <div class="col-span-12 lg:col-span-4">
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
        <div class="col-span-12">
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
@endsection