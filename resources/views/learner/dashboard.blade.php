@extends('layouts.app')

@section('content')
<div class="main-content w-full pb-8">
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Welcome back, {{ auth()->user()->first_name ?? 'Learner' }}!
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Here's your training progress overview
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 dark:text-navy-300">
                        {{ now()->format('l, F j, Y') }}
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Training Hours -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Training Hours</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">156</h3>
                            <p class="text-xs text-success">This month</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Attendance Rate -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Attendance</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">92%</h3>
                            <p class="text-xs text-success">Excellent</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-user-check text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Programs -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Programs</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">2</h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Active</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-graduation-cap text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Monthly Earnings -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">This Month</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">R 8,640</h3>
                            <p class="text-xs text-success">+ R 320 allowances</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                            <i class="fa fa-coins text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                <!-- Upcoming Sessions -->
                <div class="col-span-1 lg:col-span-2">
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Upcoming Training Sessions
                            </h2>
                            <a href="#" class="border-b border-dotted border-current pb-0.5 text-xs+ font-medium text-primary outline-none transition-colors duration-300 hover:text-primary/70 focus:text-primary/70">
                                View All
                            </a>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="space-y-3.5">
                                <!-- Session 1 -->
                                <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                    <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                                        <i class="fa fa-code text-primary"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                            Web Development Fundamentals
                                        </h3>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            Room 101 • 08:00 - 16:00
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            Tomorrow, {{ now()->addDay()->format('M j') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge bg-success text-white">Confirmed</span>
                                        <button class="btn mt-1 size-6 rounded-full bg-primary/10 p-0 font-medium text-primary hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25">
                                            <i class="fa fa-qrcode"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Session 2 -->
                                <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                    <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                                        <i class="fa fa-database text-warning"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                            Database Design Principles
                                        </h3>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            Room 205 • 08:00 - 12:00
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            {{ now()->addDays(2)->format('D, M j') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge bg-warning text-white">Pending</span>
                                    </div>
                                </div>

                                <!-- Session 3 -->
                                <div class="flex items-center space-x-3 rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                    <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                                        <i class="fa fa-laptop text-info"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                            Portfolio Development
                                        </h3>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            Online Session • 14:00 - 17:00
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            {{ now()->addDays(5)->format('D, M j') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge bg-info text-white">Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               <!-- Quick Actions & Recent Activity -->
             <div class="space-y-4 sm:space-y-5">
             <!-- Quick Actions -->
             <div class="card">
              <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                 <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
                    Quick Actions
                 </h2>
              </div>
             <div class="px-4 pb-4 sm:px-5">
                 <div class="space-y-2">
                    {{-- Changed from $recentActivities to only show payslip download if available --}}
                      {{-- Check if user has any payslips, not just in recent activities --}}
@php
    $userPayslips = auth()->user()->payslips()->latest()->first();
@endphp

@if($userPayslips)
    <a href="{{ route('payslips.download', $userPayslips->id) }}"
       class="flex items-center justify-between rounded-lg bg-slate-100 p-3 transition-colors hover:bg-slate-200 dark:bg-navy-600 dark:hover:bg-navy-500">
        <div class="flex items-center space-x-3">
            <div class="mask is-squircle flex size-8 items-center justify-center bg-primary/10">
                <i class="fa fa-download text-primary text-xs"></i>
            </div>
            <span class="text-sm font-medium">Download Latest Payslip</span>
        </div>
        <i class="fa fa-chevron-right text-xs text-slate-400"></i>
    </a>
@else
    {{-- Show message if no payslips available --}}
    <div class="flex items-center justify-between rounded-lg bg-slate-100 p-3 opacity-50 dark:bg-navy-600">
        <div class="flex items-center space-x-3">
            <div class="mask is-squircle flex size-8 items-center justify-center bg-slate-400/10">
                <i class="fa fa-download text-slate-400 text-xs"></i>
            </div>
            <span class="text-sm font-medium text-slate-400">No payslips available</span>
        </div>
    </div>
@endif

                <a href="{{ route('attendance.index') }}"
                   class="flex items-center justify-between rounded-lg bg-slate-100 p-3 transition-colors hover:bg-slate-200 dark:bg-navy-600 dark:hover:bg-navy-500">
                    <div class="flex items-center space-x-3">
                        <div class="mask is-squircle flex size-8 items-center justify-center bg-success/10">
                            <i class="fa fa-calendar-check text-success text-xs"></i>
                        </div>
                        <span class="text-sm font-medium">View Attendance</span>
                    </div>
                    <i class="fa fa-chevron-right text-xs text-slate-400"></i>
                </a>

                {{--  Changed route name from 'tax.certificate' to 'compliance.tax_certificates.index' --}}
                <a href="{{ route('compliance.tax_certificates.index') }}" 
                   class="flex items-center justify-between rounded-lg bg-slate-100 p-3 transition-colors hover:bg-slate-200 dark:bg-navy-600 dark:hover:bg-navy-500">
                    <div class="flex items-center space-x-3">
                        <div class="mask is-squircle flex size-8 items-center justify-center bg-warning/10">
                            <i class="fa fa-certificate text-warning text-xs"></i>
                        </div>
                        <span class="text-sm font-medium">Tax Certificates</span>
                    </div>
                    <i class="fa fa-chevron-right text-xs text-slate-400"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
            <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
                Recent Activity
            </h2>
        </div>
        <div class="px-4 pb-4 sm:px-5">
            <div class="space-y-4">
                {{--Changed from $activities to $recentActivities --}}
                @forelse($recentActivities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="mask is-squircle flex size-8 items-center justify-center 
                            {{ $activity['type'] === 'attendance' ? 'bg-success/10' : '' }}
                            {{ $activity['type'] === 'payslip' ? 'bg-primary/10' : '' }}
                            {{ $activity['type'] === 'leave' ? 'bg-warning/10' : '' }}">
                            <i class="fa 
                                {{ $activity['type'] === 'attendance' ? 'fa-check text-success' : '' }}
                                {{ $activity['type'] === 'payslip' ? 'fa-file text-primary' : '' }}
                                {{ $activity['type'] === 'leave' ? 'fa-calendar text-warning' : '' }}
                                text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                {{ $activity['title'] }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-navy-300">
                                {{ $activity['description'] }} • {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No recent activity available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
@endsection