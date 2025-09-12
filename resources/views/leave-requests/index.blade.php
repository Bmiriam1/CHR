@extends('layouts.app')

@section('title', 'Leave Management')

@section('content')
    <div
        class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        
        <!-- Main Content Area -->
        <div class="col-span-12 lg:col-span-8">
            <!-- Page Header -->
            <div class="flex items-center justify-between space-x-2 mb-6">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                        Leave Management Dashboard
                    </h2>
                    <p class="mt-1 text-xs-plus text-slate-500 dark:text-navy-200">
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

            <!-- Leave Balance Overview -->
            <div class="card col-span-12">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Leave Balances
                    </h2>
                    <a href="{{ route('leave-requests.balances') }}"
                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    @if(isset($balanceSummary['balances']))
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($balanceSummary['balances'] as $leaveType => $balance)
                                <div class="flex items-center justify-between space-x-2 rounded-lg border border-slate-200 p-4 dark:border-navy-500">
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
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto size-12 text-slate-400 dark:text-navy-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-slate-500 dark:text-navy-200">No leave balances available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Leave Requests -->
            <div class="card col-span-12 mt-4">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Recent Leave Requests
                    </h2>
                    <a href="{{ route('leave-requests.index') }}"
                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    @if($recentRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentRequests as $request)
                                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'error' : 'warning') }}/10">
                                            <svg class="size-5 text-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'error' : 'warning') }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $request->leaveType->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-navy-200">
                                                {{ $request->start_date->format('M j, Y') }} - {{ $request->end_date->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="badge rounded-full {{ $request->status_badge_class }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        <a href="{{ route('leave-requests.show', $request) }}"
                                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto size-12 text-slate-400 dark:text-navy-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-slate-500 dark:text-navy-200">No leave requests found</p>
                            <a href="{{ route('leave-requests.create') }}" class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 mt-4">
                                Create First Request
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-span-12 lg:col-span-4">
            <!-- Service Information -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-info/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Service Information
                            </h3>
                        </div>
                    </div>
                </div>
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
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                    Probation End Date
                                </p>
                                <p class="text-slate-700 dark:text-navy-100">{{ $balanceSummary['service_info']['probation_end_date']->format('M j, Y') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Expiring Carry Over Days -->
            @if(isset($expiringCarryOver) && count($expiringCarryOver) > 0)
                <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                    <div class="flex items-center justify-between space-x-2">
                        <div class="flex items-center space-x-3">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-warning/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Expiring Leave Days
                                </h3>
                            </div>
                        </div>
                    </div>
                    @foreach($expiringCarryOver as $carryOver)
                        <div class="alert rounded-lg bg-warning/10 text-warning">
                            <p class="text-sm">
                                <strong>{{ $carryOver['remaining_days'] }} {{ $carryOver['leave_type'] }} days</strong> 
                                expire on {{ $carryOver['expiry_date']->format('M j, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Quick Actions
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('leave-requests.create') }}"
                        class="btn w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Leave Request
                    </a>
                    <a href="{{ route('leave-requests.balances') }}"
                        class="btn w-full bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        View Balances
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection