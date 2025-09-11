@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Leave Management Dashboard
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        SA Employment Law Compliant Leave System
                    </p>
                </div>
                <a href="{{ route('leave-requests.create') }}"
                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Leave Request
                </a>
            </div>

            <!-- Leave Balance Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                @if(isset($balanceSummary['balances']))
                    @foreach($balanceSummary['balances'] as $leaveType => $balance)
                        <div class="card">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs+ font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400">
                                            {{ ucwords(str_replace('_', ' ', $leaveType)) }}
                                        </p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $balance['current'] ?? 0 }} days
                                        </p>
                                        @if(isset($balance['accrued_this_year']))
                                            <p class="text-xs text-success">
                                                +{{ $balance['accrued_this_year'] }} accrued this year
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-{{ $balance['current'] > 5 ? 'success' : ($balance['current'] > 0 ? 'warning' : 'error') }}/10">
                                        <svg class="size-5 text-{{ $balance['current'] > 5 ? 'success' : ($balance['current'] > 0 ? 'warning' : 'error') }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                @if(isset($balance['carry_over']) && $balance['carry_over'] > 0)
                                    <p class="text-xs text-info mt-2">
                                        {{ $balance['carry_over'] }} days carried over
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full">
                        <div class="card">
                            <div class="p-4">
                                <div class="alert rounded-lg bg-info/10 text-info">
                                    <div class="flex">
                                        <svg class="size-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium">Welcome to Leave Management!</h4>
                                            <p class="mt-1 text-sm">Your leave balances will appear here once initialized. Contact HR to set up your leave allocation.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <!-- Recent Leave Requests -->
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-navy-500">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Recent Leave Requests
                            </h3>
                            <a href="{{ route('leave-requests.balances') }}" class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450">
                                View All
                            </a>
                        </div>
                        <div class="p-4">
                            @if($recentRequests && $recentRequests->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recentRequests as $request)
                                        <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3 dark:border-navy-500">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 dark:bg-navy-600">
                                                    <svg class="size-5 text-slate-500 dark:text-navy-200" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-slate-700 dark:text-navy-100">
                                                        {{ $request->leaveType->name ?? 'Unknown Leave Type' }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 dark:text-navy-300">
                                                        {{ $request->start_date->format('M j') }} - {{ $request->end_date->format('M j, Y') }} ({{ $request->duration }} days)
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="badge rounded-full {{ $request->status_badge_class }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                                <a href="{{ route('leave-requests.show', $request) }}" class="btn size-7 rounded-full p-0 hover:bg-slate-300/20">
                                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto size-12 text-slate-400 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v2a2 2 0 002 2h4a2 2 0 002-2v-2M8 11V7a4 4 0 118 0v4M8 11h8" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-500 dark:text-navy-400">No leave requests yet</h3>
                                    <p class="mt-1 text-sm text-slate-400 dark:text-navy-300">Your submitted leave requests will appear here</p>
                                    <div class="mt-4">
                                        <a href="{{ route('leave-requests.create') }}" class="btn bg-primary text-white">Submit First Request</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Service Information & Quick Actions -->
                <div class="space-y-4">
                    <!-- Service Information -->
                    <div class="card">
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-3">
                                Service Information
                            </h3>
                            @if(isset($balanceSummary['service_info']))
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                            Employment Status
                                        </p>
                                        <span class="badge rounded-full {{ $balanceSummary['service_info']['is_probation'] ? 'bg-warning/10 text-warning' : 'bg-success/10 text-success' }}">
                                            {{ $balanceSummary['service_info']['is_probation'] ? 'Probation Period' : 'Permanent Employee' }}
                                        </span>
                                    </div>
                                    
                                    @if(isset($balanceSummary['service_info']['service_months']))
                                        <div>
                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                                Service Period
                                            </p>
                                            <p class="text-slate-700 dark:text-navy-100">{{ $balanceSummary['service_info']['service_months'] }} months</p>
                                        </div>
                                    @endif

                                    @if($balanceSummary['service_info']['is_probation'] ?? false)
                                        <div class="alert rounded-lg bg-warning/10 text-warning">
                                            <div class="flex">
                                                <svg class="size-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                <div class="ml-3">
                                                    <p class="text-sm"><strong>Probation Period:</strong> Leave accrual and eligibility may be limited as per BCEA requirements.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('leave-requests.balances') }}" class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 w-full">
                                    View Detailed Balances
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Expiring Carry Over -->
                    @if(isset($expiringCarryOver) && count($expiringCarryOver) > 0)
                        <div class="card">
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-warning mb-3">
                                    <svg class="inline size-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Expiring Leave Days
                                </h3>
                                @foreach($expiringCarryOver as $carryOver)
                                    <div class="alert rounded-lg bg-warning/10 text-warning mb-2">
                                        <p class="text-sm">
                                            <strong>{{ $carryOver['remaining_days'] }} {{ $carryOver['leave_type'] }} days</strong> 
                                            expire on {{ $carryOver['expiry_date']->format('M j, Y') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Getting Started Guide -->
                    <div class="card">
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-3">
                                SA Leave Types (BCEA Compliant)
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <svg class="size-4 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong>Annual:</strong> 21 days/year</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="size-4 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong>Sick:</strong> 36 days/3 years</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="size-4 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong>Maternity:</strong> 4 months</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="size-4 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span><strong>Family:</strong> 3 days/year</span>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <a href="{{ route('leave-requests.create') }}" class="btn bg-primary text-white w-full">
                                    <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Submit Leave Request
                                </a>
                                <a href="{{ route('leave-requests.balances') }}" class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 w-full">
                                    <svg class="mr-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    View Balances
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection