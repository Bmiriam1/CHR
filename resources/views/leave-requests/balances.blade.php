@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
    <div
        class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        
        <!-- Main Content Area -->
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between space-x-2 mb-6">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                        Leave Balances
                    </h2>
                    <p class="mt-1 text-xs-plus text-slate-500 dark:text-navy-200">
                        Detailed view of your leave entitlements and usage
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('leave-requests.create') }}"
                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Request
                    </a>
                    <a href="{{ route('leave-requests.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Leave
                    </a>
                </div>
            </div>

            <!-- Leave Balance Summary -->
            <div class="card col-span-12">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Balance Overview
                    </h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-500 dark:text-navy-200">Year:</span>
                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $balanceSummary['balance_record']->leave_year ?? date('Y') }}</span>
                    </div>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    @if(isset($balanceSummary['balances']))
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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

            <!-- Detailed Leave Types -->
            <div class="card col-span-12 mt-4">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Leave Type Details
                    </h2>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    <div class="space-y-4">
                        @foreach($leaveTypes as $leaveType)
                            @php
                                $balance = $balanceSummary['balances'][strtolower($leaveType->code)] ?? null;
                            @endphp
                            <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-{{ $balance && $balance['current'] > 0 ? 'success' : 'slate' }}/10">
                                            <svg class="size-5 text-{{ $balance && $balance['current'] > 0 ? 'success' : 'slate' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveType->name }}</h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-200">{{ $leaveType->description }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $balance['current'] ?? 0 }} days
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-navy-200">available</p>
                                    </div>
                                </div>
                                
                                @if($balance)
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-slate-500 dark:text-navy-200">Accrued This Year</p>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $balance['accrued_this_year'] ?? 0 }} days</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-navy-200">Taken This Year</p>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $balance['taken_this_year'] ?? 0 }} days</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Accrual History -->
            @if(isset($accrualHistory) && $accrualHistory->count() > 0)
                <div class="card col-span-12 mt-4">
                    <div class="flex items-center justify-between py-3 px-4">
                        <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Recent Accruals
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="space-y-3">
                            @foreach($accrualHistory as $accrual)
                                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-success/10">
                                            <svg class="size-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $accrual->leaveType->name }}
                                            </p>
                                            <p class="text-sm text-slate-500 dark:text-navy-200">
                                                {{ $accrual->accrual_date->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-success">+{{ $accrual->days_accrued }} days</p>
                                        <p class="text-xs text-slate-500 dark:text-navy-200">accrued</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
