@extends('layouts.app')

@section('title', 'HR Dashboard')

@section('content')
    <div
        class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12 lg:col-span-8">
            <div class="flex items-center justify-between space-x-2">
                <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                    Attendance & Earnings Overview
                </h2>
                <div x-data="{activeTab:'tabRecent'}"
                    class="is-scrollbar-hidden overflow-x-auto rounded-lg bg-slate-200 text-slate-600 dark:bg-navy-800 dark:text-navy-200">
                    <div class="tabs-list flex p-1">
                        <button @click="activeTab = 'tabRecent'"
                            :class="activeTab === 'tabRecent' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            This Month
                        </button>
                        <button @click="activeTab = 'tabAll'"
                            :class="activeTab === 'tabAll' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            Year to Date
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:space-x-7">
                <div class="mt-4 flex shrink-0 flex-col items-center sm:items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-success" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <div class="mt-4">
                        <div class="flex items-center space-x-1">
                            @php
                                $attendanceTotal = ($trends['attendance_records']['value'] ?? 0);
                                $attendanceRate = $attendanceTotal > 0 ? (($trends['present_days']['value'] ?? 0) / $attendanceTotal) * 100 : 0;
                            @endphp
                            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($attendanceRate, 1) }}%
                            </p>
                            <button
                                class="btn size-6 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            attendance rate
                        </p>
                    </div>
                    <div class="mt-3 flex items-center space-x-2">
                        <div class="ax-transparent-gridline w-28">
                            <div
                                x-init="$nextTick(() => { 
                                    var attendanceData = {
                                        series: [{{ $attendanceRate }}],
                                        options: {
                                            chart: {
                                                type: 'radialBar',
                                                height: 100,
                                                sparkline: { enabled: true }
                                            },
                                            plotOptions: {
                                                radialBar: {
                                                    hollow: { margin: 0, size: '50%' },
                                                    track: { margin: 1 },
                                                    dataLabels: { show: false }
                                                }
                                            },
                                            colors: ['#10b981']
                                        }
                                    };
                                    $el._x_chart = new ApexCharts($el, attendanceData); 
                                    $el._x_chart.render(); 
                                });">
                            </div>
                        </div>
                        <div class="flex items-center space-x-0.5">
                            @if($attendanceRate >= 90)
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-success" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                            @elseif($attendanceRate >= 75)
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-warning" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-error" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            @endif
                            <p class="text-sm-plus {{ $attendanceRate >= 90 ? 'text-success' : ($attendanceRate >= 75 ? 'text-warning' : 'text-error') }} dark:text-navy-100">
                                {{ $trends['present_days']['value'] ?? 0 }}/{{ $attendanceTotal }}
                            </p>
                        </div>
                    </div>
                    <button
                        class="btn mt-8 space-x-2 rounded-full border border-slate-300 px-3 text-xs-plus font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90"
                        onclick="window.location.href='{{ route('attendance.summary') }}'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5 text-slate-400 dark:text-navy-300"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                        </svg>
                        <span>Download Report</span>
                    </button>
                </div>

                <div class="ax-transparent-gridline grid w-full grid-cols-1">
                    <div
                        x-init="$nextTick(() => { 
                            var monthlyData = {
                                series: [{
                                    name: 'Present Days',
                                    data: [
                                        {{ $trends['present_days']['value'] ?? 0 }},
                                        {{ ($previousMetrics['present_days'] ?? 0) }},
                                        {{ max(0, ($trends['present_days']['value'] ?? 0) - 5) }},
                                        {{ max(0, ($trends['present_days']['value'] ?? 0) - 3) }},
                                        {{ max(0, ($trends['present_days']['value'] ?? 0) - 8) }},
                                        {{ max(0, ($trends['present_days']['value'] ?? 0) - 2) }}
                                    ]
                                }, {
                                    name: 'Absent Days',
                                    data: [
                                        {{ $trends['absent_days']['value'] ?? 0 }},
                                        {{ ($previousMetrics['absent_days'] ?? 0) }},
                                        {{ max(0, ($trends['absent_days']['value'] ?? 0) - 2) }},
                                        {{ max(0, ($trends['absent_days']['value'] ?? 0) - 1) }},
                                        {{ max(0, ($trends['absent_days']['value'] ?? 0) + 2) }},
                                        {{ max(0, ($trends['absent_days']['value'] ?? 0) + 1) }}
                                    ]
                                }],
                                options: {
                                    chart: {
                                        type: 'area',
                                        height: 300,
                                        toolbar: { show: false }
                                    },
                                    colors: ['#10b981', '#f59e0b'],
                                    fill: {
                                        type: 'gradient',
                                        gradient: {
                                            shadeIntensity: 1,
                                            opacityFrom: 0.3,
                                            opacityTo: 0.1
                                        }
                                    },
                                    dataLabels: { enabled: false },
                                    stroke: { curve: 'smooth', width: 2 },
                                    grid: {
                                        borderColor: '#e2e8f0',
                                        strokeDashArray: 3
                                    },
                                    xaxis: {
                                        categories: ['This Month', 'Last Month', 'Month -2', 'Month -3', 'Month -4', 'Month -5'],
                                        axisBorder: { show: false },
                                        axisTicks: { show: false }
                                    },
                                    yaxis: {
                                        show: true,
                                        title: { text: 'Days' }
                                    },
                                    legend: {
                                        position: 'top',
                                        horizontalAlign: 'right'
                                    }
                                }
                            };
                            $el._x_chart = new ApexCharts($el, monthlyData); 
                            $el._x_chart.render(); 
                        });">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-2">
                <!-- Monthly Earnings -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            R{{ number_format($trends['total_gross_pay']['value'] ?? 0, 0) }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary dark:text-accent" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Monthly Earnings</p>
                </div>
                
                <!-- Present Days -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $trends['present_days']['value'] ?? 0 }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Present Days</p>
                </div>
                
                <!-- Active Employees -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $trends['total_employees']['value'] ?? 0 }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Active Employees</p>
                </div>
                
                <!-- Active Programs -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $trends['active_programs']['value'] ?? 0 }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Training Programs</p>
                </div>
                
                <!-- Payslips Generated -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $trends['payslips_generated']['value'] ?? 0 }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-secondary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Payslips</p>
                </div>
                
                <!-- Total Learners -->
                <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
                    <div class="flex justify-between">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $trends['total_learners']['value'] ?? 0 }}
                        </p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-error" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <p class="mt-1 text-xs-plus">Learners</p>
                </div>
            </div>
        </div>

        <!-- Main Activity Table -->
        <div class="card col-span-12 lg:col-span-8">
            <div class="flex items-center justify-between py-3 px-4">
                <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                    Recent HR Activities
                </h2>
                <div x-data="usePopper({placement:'bottom-end',offset:4})"
                    @click.outside="isShowPopper && (isShowPopper = false)" class="inline-flex">
                    <button x-ref="popperRef" @click="isShowPopper = !isShowPopper"
                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </button>

                    <div x-ref="popperRoot" class="popper-root" :class="isShowPopper && 'show'">
                        <div
                            class="popper-box rounded-md border border-slate-150 bg-white py-1.5 font-inter dark:border-navy-500 dark:bg-navy-700">
                            <ul>
                                <li>
                                    <a href="{{ route('attendance.index') }}"
                                        class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">View Attendance</a>
                                </li>
                                <li>
                                    <a href="{{ route('payslips.index') }}"
                                        class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">View Payslips</a>
                                </li>
                                <li>
                                    <a href="{{ route('programs.index') }}"
                                        class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100">View Programs</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="scrollbar-sm max-h-80 overflow-y-auto">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                            <tr>
                                <th
                                    class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Activity
                                </th>
                                <th
                                    class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Employee
                                </th>
                                <th
                                    class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Date
                                </th>
                                <th
                                    class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 lg:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="mask is-squircle flex size-8 items-center justify-center bg-{{ $activity['color'] }}/10">
                                            <i class="fa {{ $activity['icon'] }} text-{{ $activity['color'] }} text-xs"></i>
                                        </div>
                                        <span class="font-medium">{{ $activity['title'] }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 lg:px-5">
                                    {{ $activity['description'] }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 lg:px-5">
                                    {{ $activity['date']->format('M j, Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 lg:px-5">
                                    <div class="badge rounded-full border border-{{ $activity['color'] }} text-{{ $activity['color'] }}">
                                        {{ ucfirst($activity['type']) }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">
                                    <i class="fa fa-inbox text-slate-300 text-4xl mb-4"></i>
                                    <p class="text-slate-500 dark:text-navy-300">No recent activities</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tax & Compliance Card -->
        <div class="card col-span-12 lg:col-span-4">
            <div class="flex items-center justify-between py-3 px-4">
                <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                    Tax & Compliance
                </h2>
            </div>
            <div class="px-4 pb-4">
                @if(isset($complianceMetrics))
                <div class="space-y-4">
                    <!-- YTD PAYE -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="mask is-squircle flex size-8 items-center justify-center bg-error/10">
                                <i class="fa fa-receipt text-error text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">YTD PAYE</span>
                        </div>
                        <span class="font-semibold">R{{ number_format($complianceMetrics['ytd_paye'] ?? 0, 0) }}</span>
                    </div>

                    <!-- YTD UIF -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="mask is-squircle flex size-8 items-center justify-center bg-secondary/10">
                                <i class="fa fa-shield-alt text-secondary text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">YTD UIF</span>
                        </div>
                        <span class="font-semibold">R{{ number_format($complianceMetrics['ytd_uif'] ?? 0, 0) }}</span>
                    </div>

                    <!-- YTD ETI -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="mask is-squircle flex size-8 items-center justify-center bg-success/10">
                                <i class="fa fa-hand-holding-usd text-success text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">YTD ETI Benefit</span>
                        </div>
                        <span class="font-semibold text-success">R{{ number_format($complianceMetrics['ytd_eti'] ?? 0, 0) }}</span>
                    </div>

                    <hr class="border-slate-200 dark:border-navy-500">

                    <!-- Quick Actions -->
                    <div class="space-y-2">
                        <a href="{{ route('compliance.dashboard') }}" class="flex items-center justify-between rounded-lg bg-slate-100 p-3 transition-colors hover:bg-slate-200 dark:bg-navy-600 dark:hover:bg-navy-500">
                            <div class="flex items-center space-x-2">
                                <i class="fa fa-clipboard-check text-success text-sm"></i>
                                <span class="text-sm font-medium">Compliance Dashboard</span>
                            </div>
                            <i class="fa fa-chevron-right text-xs text-slate-400"></i>
                        </a>

                        <a href="{{ route('compliance.tax_certificates.index') }}" class="flex items-center justify-between rounded-lg bg-slate-100 p-3 transition-colors hover:bg-slate-200 dark:bg-navy-600 dark:hover:bg-navy-500">
                            <div class="flex items-center space-x-2">
                                <i class="fa fa-certificate text-warning text-sm"></i>
                                <span class="text-sm font-medium">Tax Certificates</span>
                            </div>
                            <i class="fa fa-chevron-right text-xs text-slate-400"></i>
                        </a>
                    </div>
                </div>
                @else
                <p class="text-slate-500 dark:text-navy-300 text-center py-4">No compliance data available</p>
                @endif
            </div>
        </div>
    </div>
@endsection