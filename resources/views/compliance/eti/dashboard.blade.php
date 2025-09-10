@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        ETI Dashboard
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Employment Tax Incentive claims and learner eligibility
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('compliance.dashboard') }}"
                        class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- ETI Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Eligible Learners -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Eligible Learners</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['eligible_learners']) }}
                            </h3>
                            <p class="text-xs text-info">ETI Qualified</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-users text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Benefit -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Monthly Benefit</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['monthly_benefit'], 2) }}
                            </h3>
                            <p class="text-xs text-success">{{ now()->format('F Y') }}</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-coins text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- YTD Benefit -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD Benefit</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['ytd_benefit'], 2) }}
                            </h3>
                            <p class="text-xs text-primary">{{ now()->year }}</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-chart-line text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Average per Learner -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Avg per Learner</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R
                                {{ number_format($stats['average_benefit_per_learner'], 2) }}
                            </h3>
                            <p class="text-xs text-warning">Monthly</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-calculator text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ETI Eligible Learners -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            ETI Eligible Learners
                        </h2>
                        <span class="badge bg-info text-white">{{ $etiLearners->count() }}</span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        @if($etiLearners->count() > 0)
                            <div class="space-y-3">
                                @foreach($etiLearners as $learner)
                                    <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                        <div class="flex size-10 items-center justify-center rounded-full bg-info/10">
                                            <i class="fa fa-user text-info"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $learner->first_name }} {{ $learner->last_name }}
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                ID: {{ $learner->id_number }} | Employee #: {{ $learner->employee_number }}
                                            </p>
                                            <div class="mt-1 flex items-center space-x-2">
                                                @foreach($learner->programs as $program)
                                                    <span class="badge bg-success text-white text-xs">
                                                        {{ $program->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                R {{ number_format($learner->programs->sum('eti_monthly_amount'), 2) }}
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300">Monthly</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-slate-400 dark:text-navy-300 mb-2">
                                    <i class="fa fa-users text-4xl"></i>
                                </div>
                                <p class="text-slate-500 dark:text-navy-400">No ETI eligible learners</p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                    Learners must be enrolled in ETI-eligible programs
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly ETI Claims -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Monthly ETI Claims
                        </h2>
                        <span class="badge bg-success text-white">{{ now()->year }}</span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        @if($monthlyClaims->count() > 0)
                            <div class="space-y-3">
                                @foreach($monthlyClaims as $claim)
                                    <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                        <div class="flex size-10 items-center justify-center rounded-full bg-success/10">
                                            <i class="fa fa-calendar text-success"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ \Carbon\Carbon::create(null, $claim->month)->format('F') }} {{ now()->year }}
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                ETI benefit claimed for the month
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                R {{ number_format($claim->total_benefit, 2) }}
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300">Total Benefit</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-slate-400 dark:text-navy-300 mb-2">
                                    <i class="fa fa-chart-bar text-4xl"></i>
                                </div>
                                <p class="text-slate-500 dark:text-navy-400">No ETI claims this year</p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                    Claims will appear here once learners start earning ETI benefits
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ETI Information -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            ETI Information
                        </h2>
                        <span class="badge bg-info text-white">Employment Tax Incentive</span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-lg bg-info/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">ETI Eligibility</h3>
                                <ul class="text-sm text-slate-500 dark:text-navy-300 mt-2 space-y-1">
                                    <li>• Learners aged 18-29 years</li>
                                    <li>• Enrolled in learnership programs</li>
                                    <li>• Monthly salary below R6,000</li>
                                    <li>• Must be South African citizens</li>
                                </ul>
                            </div>
                            <div class="rounded-lg bg-success/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Benefit Amounts</h3>
                                <ul class="text-sm text-slate-500 dark:text-navy-300 mt-2 space-y-1">
                                    <li>• First 12 months: R1,000/month</li>
                                    <li>• Next 12 months: R500/month</li>
                                    <li>• Maximum 24 months per learner</li>
                                    <li>• Claimed via PAYE return</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

