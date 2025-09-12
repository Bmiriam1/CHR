@extends('layouts.app')

@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Employment Tax Incentive Dashboard
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        ETI claims and learner eligibility management
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

            <!-- ETI Quick Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Eligible Learners -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ number_format($stats['eligible_learners']) }}</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Eligible Learners</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                                <i class="fa fa-users text-info text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Benefit -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">R{{ number_format($stats['monthly_benefit'], 0) }}</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Monthly Benefit</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                                <i class="fa fa-coins text-success text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- YTD Benefit -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">R{{ number_format($stats['ytd_benefit'], 0) }}</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">YTD Benefit</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                                <i class="fa fa-chart-line text-primary text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average per Learner -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">R{{ number_format($stats['average_benefit_per_learner'], 0) }}</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Avg per Learner</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                                <i class="fa fa-calculator text-warning text-lg"></i>
                            </div>
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
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($etiLearners as $learner)
                                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-200 dark:bg-navy-700 dark:border-navy-600 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                                        <!-- Decorative Elements -->
                                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-info/10 to-success/10 rounded-full -translate-y-10 translate-x-10"></div>
                                        
                                        <!-- Learner Header -->
                                        <div class="relative flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex size-12 items-center justify-center rounded-full bg-gradient-to-br from-info to-success text-white shadow-lg">
                                                    <i class="fas fa-user text-lg"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-slate-800 dark:text-navy-100 text-lg">{{ $learner->first_name }} {{ $learner->last_name }}</h4>
                                                    <p class="text-sm text-slate-500 dark:text-navy-300">Employee #{{ $learner->employee_number }}</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-info/10 text-info">
                                                <div class="w-2 h-2 rounded-full mr-2 bg-info"></div>
                                                ETI Eligible
                                            </span>
                                        </div>

                                        <!-- ID Number -->
                                        <div class="relative mb-4">
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mb-1">ID Number</p>
                                            <p class="font-mono text-sm text-slate-700 dark:text-navy-100">{{ $learner->id_number }}</p>
                                        </div>

                                        <!-- Programs -->
                                        <div class="relative mb-4">
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mb-2">Programs</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($learner->programs as $program)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success border border-success/20">
                                                        {{ $program->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Monthly Benefit -->
                                        <div class="relative pt-4 border-t border-slate-200 dark:border-navy-600">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-slate-500 dark:text-navy-300">Monthly Benefit</span>
                                                <span class="text-xl font-bold text-success">R{{ number_format($learner->programs->sum('eti_monthly_amount'), 0) }}</span>
                                            </div>
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
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($monthlyClaims as $claim)
                                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg border border-slate-200 dark:bg-navy-700 dark:border-navy-600 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                                        <!-- Decorative Background -->
                                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-success/10 to-primary/10 rounded-full -translate-y-8 translate-x-8"></div>
                                        
                                        <!-- Claim Header -->
                                        <div class="relative flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex size-12 items-center justify-center rounded-full bg-gradient-to-br from-success to-primary text-white shadow-lg">
                                                    <i class="fas fa-calendar-check text-lg"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-slate-800 dark:text-navy-100 text-lg">
                                                        {{ \Carbon\Carbon::create(null, $claim->month)->format('F') }} {{ now()->year }}
                                                    </h4>
                                                    <p class="text-sm text-slate-500 dark:text-navy-300">Monthly ETI Claim</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Claim Amount -->
                                        <div class="relative text-center py-4">
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mb-1">Total Benefit Claimed</p>
                                            <p class="text-3xl font-bold text-success">R{{ number_format($claim->total_benefit, 0) }}</p>
                                        </div>

                                        <!-- Status -->
                                        <div class="relative pt-4 border-t border-slate-200 dark:border-navy-600">
                                            <div class="flex justify-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-success/10 text-success border border-success/20">
                                                    <i class="fa fa-check-circle mr-1"></i>
                                                    Processed
                                                </span>
                                            </div>
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
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- ETI Eligibility -->
                <div class="card">
                    <div class="px-4 py-4">
                        <div class="flex items-center mb-4">
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10 mr-3">
                                <i class="fa fa-check-circle text-info"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">ETI Eligibility</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-birthday-cake text-info text-sm"></i>
                                <span class="text-sm text-slate-600 dark:text-navy-300">Learners aged 18-29 years</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-graduation-cap text-info text-sm"></i>
                                <span class="text-sm text-slate-600 dark:text-navy-300">Enrolled in learnership programs</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-money-bill-wave text-info text-sm"></i>
                                <span class="text-sm text-slate-600 dark:text-navy-300">Monthly salary below R6,000</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-flag text-info text-sm"></i>
                                <span class="text-sm text-slate-600 dark:text-navy-300">Must be South African citizens</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Benefit Amounts -->
                <div class="card">
                    <div class="px-4 py-4">
                        <div class="flex items-center mb-4">
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10 mr-3">
                                <i class="fa fa-trophy text-success"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Benefit Amounts</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-navy-300">First 12 months</span>
                                <span class="font-medium text-success">R1,000/month</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Next 12 months</span>
                                <span class="font-medium text-success">R500/month</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Maximum duration</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">24 months</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Claimed via</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    PAYE Return
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

