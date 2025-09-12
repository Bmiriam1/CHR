@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
    <div
        class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">

        <!-- Main Content Area -->
        <div class="col-span-12 lg:col-span-8">
            <!-- Page Header -->
            <div class="flex items-center justify-between space-x-2 mb-6">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                        Leave Application - {{ $user->first_name }} {{ $user->last_name }}
                    </h2>
                    <p class="mt-1 text-xs-plus text-slate-500 dark:text-navy-200">
                        Program: {{ $program->title }} | Year: {{ $leaveBalance->leave_year }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('programs.show', $program) }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        View Program
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

            <!-- Leave Request Details -->
            <div class="card col-span-12">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Request Details
                    </h2>
                    <span class="badge rounded-full {{ $leaveRequest->status_badge_class }}">
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100 mb-3">Leave Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                        Leave Type
                                    </p>
                                    <p class="text-slate-700 dark:text-navy-100">{{ $leaveRequest->leaveType->name }}</p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                        Duration
                                    </p>
                                    <p class="text-slate-700 dark:text-navy-100">
                                        {{ $leaveRequest->start_date->format('M j, Y') }} -
                                        {{ $leaveRequest->end_date->format('M j, Y') }}
                                        ({{ $leaveRequest->duration }} days)
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                        Reason
                                    </p>
                                    <p class="text-slate-700 dark:text-navy-100">{{ $leaveRequest->reason }}</p>
                                </div>
                                @if($leaveRequest->notes)
                                    <div>
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                            Notes
                                        </p>
                                        <p class="text-slate-700 dark:text-navy-100">{{ $leaveRequest->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100 mb-3">Request Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p
                                        class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                        Submitted
                                    </p>
                                    <p class="text-slate-700 dark:text-navy-100">
                                        {{ $leaveRequest->submitted_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @if($leaveRequest->approved_at)
                                    <div>
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                            {{ $leaveRequest->status === 'approved' ? 'Approved' : 'Processed' }}
                                        </p>
                                        <p class="text-slate-700 dark:text-navy-100">
                                            {{ $leaveRequest->approved_at->format('M j, Y g:i A') }}</p>
                                        @if($leaveRequest->approvedBy)
                                            <p class="text-xs text-slate-500 dark:text-navy-200">by
                                                {{ $leaveRequest->approvedBy->full_name }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if($leaveRequest->is_emergency)
                                    <div>
                                        <span class="badge rounded-full bg-error/10 text-error">
                                            Emergency Request
                                        </span>
                                    </div>
                                @endif
                                @if($leaveRequest->requires_medical_certificate)
                                    <div>
                                        <span class="badge rounded-full bg-warning/10 text-warning">
                                            Medical Certificate Required
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            @if($leaveRequest->is_paid_leave)
                <div class="card col-span-12 mt-4">
                    <div class="flex items-center justify-between py-3 px-4">
                        <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Financial Information
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                    Daily Rate
                                </p>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                    R{{ number_format($leaveRequest->daily_rate_at_time, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                    Total Days
                                </p>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                    {{ $leaveRequest->duration }} days
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-navy-400 mb-1">
                                    Total Pay
                                </p>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                                    R{{ number_format($leaveRequest->total_leave_pay, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if($leaveRequest->status === 'pending')
                <div class="card col-span-12 mt-4">
                    <div class="flex items-center justify-between py-3 px-4">
                        <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Actions
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="flex space-x-3">
                            <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90"
                                    onclick="return confirm('Are you sure you want to cancel this leave request?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-span-12 lg:col-span-4">
            <!-- Leave Balance Summary -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-info/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Leave Balance Summary
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Total Entitled</span>
                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_entitled }}
                            days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Total Taken</span>
                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_taken }}
                            days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Remaining</span>
                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_balance }}
                            days</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 dark:border-navy-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-slate-600 dark:text-navy-200">Utilization</span>
                            <span
                                class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->getUtilizationPercentage() }}%</span>
                        </div>
                        <div class="h-2 rounded-full {{ $leaveBalance->getBalanceStatus() === 'critical' ? 'bg-red-500' : ($leaveBalance->getBalanceStatus() === 'warning' ? 'bg-yellow-500' : ($leaveBalance->getBalanceStatus() === 'moderate' ? 'bg-blue-500' : 'bg-green-500')) }}"
                            style="width: {{ $leaveBalance->getUtilizationPercentage() }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Leave Type Breakdown -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Leave Breakdown
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Sick Leave</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $leaveBalance->sick_leave_taken }}/{{ $leaveBalance->sick_leave_entitled }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Personal Leave</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $leaveBalance->personal_leave_taken }}/{{ $leaveBalance->personal_leave_entitled }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Emergency Leave</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $leaveBalance->emergency_leave_taken }}/{{ $leaveBalance->emergency_leave_entitled }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-200">Other Leave</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $leaveBalance->other_leave_taken }}/{{ $leaveBalance->other_leave_entitled }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-success/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
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
                        View All Balances
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection