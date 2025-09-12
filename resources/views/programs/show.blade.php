@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        {{ $program->title }}
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        {{ $program->description ?? 'No description available' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('programs.edit', $program) }}"
                        class="btn bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Program
                    </a>
                    <button onclick="confirmDelete({{ $program->id }})"
                        class="btn bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Program
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Participants -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Participants</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['total_learners'] ?? $program->enrolled_count ?? 0 }}
                            </h3>
                            <p class="text-xs text-info">Enrolled</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-users text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Progress -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Progress</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['completion_rate'] ?? $program->completion_rate ?? 0 }}%
                            </h3>
                            <p class="text-xs text-success">Complete</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Budget -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Budget</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100" id="total-budget">
                                R {{ number_format($stats['total_value'] ?? $program->getTotalValue() ?? 0, 2) }}
                            </h3>
                            <p class="text-xs text-warning">Allocated</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-coins text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Duration -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Duration</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $program->start_date && $program->end_date ? $program->start_date->diffInDays($program->end_date) : ($program->duration_months ? $program->duration_months * 30 : 0) }}
                            </h3>
                            <p class="text-xs text-primary">Days</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-calendar-alt text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="mt-8" x-data="{activeTab:'overview'}">
                <div
                    class="is-scrollbar-hidden overflow-x-auto rounded-lg bg-slate-200 text-slate-600 dark:bg-navy-800 dark:text-navy-200">
                    <div class="tabs-list flex p-1">
                        <button @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Overview
                        </button>
                        <button @click="activeTab = 'schedules'"
                            :class="activeTab === 'schedules' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Schedule
                        </button>
                        <button @click="activeTab = 'participants'"
                            :class="activeTab === 'participants' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            Participants
                        </button>
                        <button @click="activeTab = 'attendance'"
                            :class="activeTab === 'attendance' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Attendance
                        </button>
                        <button @click="activeTab = 'leave-management'"
                            :class="activeTab === 'leave-management' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Leave Management
                        </button>
                        <button @click="activeTab = 'budget'"
                            :class="activeTab === 'budget' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Budget
                        </button>
                        <button @click="activeTab = 'compliance'"
                            :class="activeTab === 'compliance' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Compliance
                        </button>
                        <button @click="activeTab = 'sim-cards'"
                            :class="activeTab === 'sim-cards' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            SIM Cards
                        </button>
                        <button @click="activeTab = 'documents'"
                            :class="activeTab === 'documents' ? 'bg-white shadow-sm dark:bg-navy-500 dark:text-navy-100' : 'hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                            class="btn shrink-0 px-3 py-1 text-xs-plus font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Documents
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="mt-4">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'">
                        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                            <!-- Program Information -->
                            <div class="lg:col-span-2">
                                <div class="card">
                                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Program Information
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Coordinator</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $program->coordinator ? ($program->coordinator->first_name . ' ' . $program->coordinator->last_name) : 'No Assigned Coordinator' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Program Type</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $program->programType->name ?? 'Not specified' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Start Date</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $program->start_date ? $program->start_date->format('M d, Y') : 'Not set' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">End Date</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $program->end_date ? $program->end_date->format('M d, Y') : 'Not set' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Daily Rate</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    R {{ number_format($program->daily_rate ?? 0, 2) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Status</p>
                                                <span
                                                    class="badge rounded-full {{ $program->getStatusColor() === 'green' ? 'bg-success' : ($program->getStatusColor() === 'blue' ? 'bg-primary' : 'bg-slate-400') }} text-white">
                                                    {{ ucfirst($program->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($program->description)
                                            <div class="mt-4">
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Description</p>
                                                <p class="text-slate-700 dark:text-navy-100">{{ $program->description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div>
                                <div class="card">
                                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Quick Actions
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="space-y-3">
                                            <a href="{{ route('programs.schedules', $program) }}"
                                                class="btn w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Manage Schedule
                                            </a>
                                            <a href="{{ route('programs.progress', $program) }}"
                                                class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                                View Progress
                                            </a>
                                            <a href="{{ route('programs.revenue-report', $program) }}"
                                                class="btn w-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                                Revenue Reports
                                            </a>
                                            <a href="{{ route('programs.client-pack', $program) }}"
                                                class="btn w-full bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Client Pack
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Tab -->
                    <div x-show="activeTab === 'schedules'">
                        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                            <!-- Schedule List -->
                            <div class="lg:col-span-2">
                                <div class="card">
                                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Training Schedules
                                        </h2>
                                        <div class="flex space-x-2">
                                            <button onclick="openScheduleModal()"
                                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Add Schedule
                                            </button>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <!-- Schedule Filters -->
                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <select id="schedule-status-filter" class="form-select text-xs">
                                                <option value="">All Status</option>
                                                <option value="scheduled">Scheduled</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                            <input type="date" id="schedule-date-filter" class="form-input text-xs"
                                                placeholder="Filter by date">
                                            <select id="schedule-type-filter" class="form-select text-xs">
                                                <option value="">All Types</option>
                                                <option value="lecture">Lecture</option>
                                                <option value="practical">Practical</option>
                                                <option value="assessment">Assessment</option>
                                                <option value="workshop">Workshop</option>
                                                <option value="seminar">Seminar</option>
                                            </select>
                                            <button onclick="filterSchedules()"
                                                class="btn bg-slate-150 text-slate-800 hover:bg-slate-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                </svg>
                                                Filter
                                            </button>
                                        </div>

                                        <!-- Schedule Cards Grid -->
                                        <div id="schedules-grid" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            @if(isset($schedules) && $schedules->count() > 0)
                                                @foreach($schedules as $schedule)
                                                    <div class="schedule-card card cursor-pointer hover:shadow-lg transition-shadow duration-200"
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        onclick="selectSchedule({{ $schedule->id }})">
                                                        <div class="p-4">
                                                            <div class="flex items-start justify-between mb-3">
                                                                <div>
                                                                    <h3 class="font-semibold text-slate-700 dark:text-navy-100">
                                                                        {{ $schedule->title ?? 'Training Session' }}
                                                                    </h3>
                                                                    <p class="text-sm text-slate-500 dark:text-navy-300">
                                                                        {{ $schedule->session_date ? $schedule->session_date->format('M d, Y') : 'No date set' }}
                                                                    </p>
                                                                </div>
                                                                <span
                                                                    class="badge bg-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'in_progress' ? 'warning' : 'primary') }} text-white">
                                                                    {{ ucfirst($schedule->status ?? 'scheduled') }}
                                                                </span>
                                                            </div>

                                                            <div class="space-y-2 mb-3">
                                                                <div
                                                                    class="flex items-center text-sm text-slate-600 dark:text-navy-200">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4"
                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    {{ $schedule->start_time ?? '09:00' }} -
                                                                    {{ $schedule->end_time ?? '17:00' }}
                                                                </div>

                                                                <div
                                                                    class="flex items-center text-sm text-slate-600 dark:text-navy-200">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4"
                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                    {{ ucfirst($schedule->session_type ?? 'lecture') }}
                                                                </div>

                                                                @if($schedule->is_online)
                                                                    <div
                                                                        class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                        </svg>
                                                                        Online Session
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="flex items-center justify-between">
                                                                <div class="text-xs text-slate-500 dark:text-navy-300">
                                                                    {{ $schedule->planned_duration_hours ?? 0 }}h duration
                                                                </div>
                                                                <div class="flex space-x-1">
                                                                    <button
                                                                        onclick="event.stopPropagation(); viewSchedule({{ $schedule->id }})"
                                                                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                    </button>
                                                                    <button
                                                                        onclick="event.stopPropagation(); editSchedule({{ $schedule->id }})"
                                                                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-span-2 text-center py-8">
                                                    <div class="text-slate-400 dark:text-navy-300 mb-2">
                                                        <i class="fa fa-calendar-alt text-4xl"></i>
                                                    </div>
                                                    <p class="text-slate-500 dark:text-navy-400">No training sessions
                                                        scheduled</p>
                                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Create your
                                                        first training session</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Details & Actions -->
                            <div class="lg:col-span-1">
                                <!-- Selected Schedule Details -->
                                <div class="card" id="schedule-details-card" style="display: none;">
                                    <div class="px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Schedule Details
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5" id="schedule-details-content">
                                        <!-- Details will be loaded here -->
                                    </div>
                                </div>

                                <!-- Attendance Actions -->
                                <div class="card mt-4" id="attendance-actions-card" style="display: none;">
                                    <div class="px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Attendance Actions
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="space-y-3">
                                            <button onclick="markAttendanceForSchedule()"
                                                class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90 w-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                Mark Attendance
                                            </button>

                                            <button onclick="viewAttendanceForSchedule()"
                                                class="btn bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 w-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                                View Attendance
                                            </button>

                                            <button onclick="generateScheduleQR()"
                                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 w-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v2" />
                                                </svg>
                                                Generate QR Code
                                            </button>

                                            <button onclick="editSelectedSchedule()"
                                                class="btn bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90 w-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Schedule
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Stats -->
                                <div class="card mt-4">
                                    <div class="px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Schedule Stats
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-slate-600 dark:text-navy-200">Total
                                                    Schedules</span>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="total-schedules-count">{{ $schedules->count() ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-slate-600 dark:text-navy-200">Completed</span>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="completed-schedules-count">{{ $schedules->where('status', 'completed')->count() ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-slate-600 dark:text-navy-200">Upcoming</span>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="upcoming-schedules-count">{{ $schedules->where('status', 'scheduled')->count() ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-slate-600 dark:text-navy-200">In
                                                    Progress</span>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="in-progress-schedules-count">{{ $schedules->where('status', 'in_progress')->count() ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participants Tab -->
                    <div x-show="activeTab === 'participants'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Program Participants
                                </h2>
                                <button onclick="openAddParticipantModal()"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Participant
                                </button>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                @if($program->participants && $program->participants->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="border-b border-slate-200 dark:border-navy-500">
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Participant
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Email
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Status
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Enrolled Date
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                                @foreach($program->participants as $participant)
                                                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-600">
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <div class="flex items-center space-x-3">
                                                                <div class="avatar size-8">
                                                                    <div
                                                                        class="is-initial rounded-full bg-primary text-xs+ text-white">
                                                                        {{ substr($participant->user->first_name, 0, 1) }}{{ substr($participant->user->last_name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <p class="font-medium text-slate-700 dark:text-navy-100">
                                                                        {{ $participant->user->first_name }}
                                                                        {{ $participant->user->last_name }}
                                                                    </p>
                                                                    <p class="text-xs text-slate-500 dark:text-navy-300">
                                                                        ID: {{ $participant->user->id_number ?? 'N/A' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span class="text-slate-600 dark:text-navy-200">
                                                                {{ $participant->user->email }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span class="badge rounded-full {{ $participant->status_badge_class }}">
                                                                {{ ucfirst($participant->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span class="text-slate-600 dark:text-navy-200">
                                                                {{ $participant->enrolled_at->format('M d, Y') }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('programs.show-participant', [$program, $participant->user]) }}"
                                                                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                                    <i class="fa fa-eye text-slate-500"></i>
                                                                </a>
                                                                <button
                                                                    onclick="openEditParticipantModal({{ $participant->user->id }}, '{{ $participant->status }}')"
                                                                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                                    <i class="fa fa-edit text-slate-500"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div class="text-slate-400 dark:text-navy-300 mb-2">
                                            <i class="fa fa-users text-4xl"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-navy-400">No participants enrolled yet</p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Start adding learners to
                                            this program</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Management Tab -->
                    <div x-show="activeTab === 'attendance'">
                        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                            <!-- Attendance Overview -->
                            <div class="lg:col-span-2">
                                <div class="card">
                                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Attendance Records
                                        </h2>
                                        <div class="flex space-x-2">
                                            <button onclick="openBulkAttendanceModal()"
                                                class="btn bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Bulk Mark
                                            </button>
                                            <button onclick="openAttendanceModal()"
                                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                Mark Attendance
                                            </button>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <!-- Attendance Filters -->
                                        <div class="mb-4 flex flex-wrap gap-2">
                                            <select id="attendance-status-filter" class="form-select text-xs">
                                                <option value="">All Status</option>
                                                <option value="present">Present</option>
                                                <option value="late">Late</option>
                                                <option value="absent_authorized">Absent (Authorized)</option>
                                                <option value="absent_unauthorized">Absent (Unauthorized)</option>
                                                <option value="excused">Excused</option>
                                                <option value="on_leave">On Leave</option>
                                                <option value="sick">Sick</option>
                                            </select>
                                            <input type="date" id="attendance-date-filter" class="form-input text-xs"
                                                placeholder="Filter by date">
                                            <select id="attendance-learner-filter" class="form-select text-xs">
                                                <option value="">All Learners</option>
                                                @if($program->participants)
                                                    @foreach($program->participants as $participant)
                                                        <option value="{{ $participant->user_id }}">
                                                            {{ $participant->user->first_name }}
                                                            {{ $participant->user->last_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button onclick="filterAttendance()"
                                                class="btn bg-slate-150 text-slate-800 hover:bg-slate-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                </svg>
                                                Filter
                                            </button>
                                        </div>

                                        <!-- Attendance Table -->
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-left">
                                                <thead>
                                                    <tr class="border-y border-slate-200 dark:border-navy-500">
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Learner
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Date
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Status
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Check In/Out
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Hours
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Payable
                                                        </th>
                                                        <th
                                                            class="whitespace-nowrap px-3 py-3 text-xs+ font-medium text-slate-500 dark:text-navy-300">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="attendance-table-body">
                                                    <!-- Attendance records will be loaded here -->
                                                    <tr>
                                                        <td colspan="7"
                                                            class="px-3 py-8 text-center text-slate-500 dark:text-navy-400">
                                                            Loading attendance records...
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Stats -->
                            <div class="lg:col-span-1">
                                <div class="card">
                                    <div class="px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            Attendance Stats
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="space-y-4">
                                            <!-- Present Days -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-success"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Present</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="present-count">0</span>
                                            </div>

                                            <!-- Late Days -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-warning"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Late</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="late-count">0</span>
                                            </div>

                                            <!-- Absent Authorized -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-info"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Absent
                                                        (Auth)</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="absent-authorized-count">0</span>
                                            </div>

                                            <!-- Absent Unauthorized -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-error"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Absent
                                                        (Unauth)</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="absent-unauthorized-count">0</span>
                                            </div>

                                            <!-- Excused -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-secondary"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Excused</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="excused-count">0</span>
                                            </div>

                                            <!-- On Leave -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-accent"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">On
                                                        Leave</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="on-leave-count">0</span>
                                            </div>

                                            <!-- Sick -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-orange-500"></div>
                                                    <span class="text-sm text-slate-600 dark:text-navy-200">Sick</span>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100"
                                                    id="sick-count">0</span>
                                            </div>

                                            <hr class="border-slate-200 dark:border-navy-500">

                                            <!-- Total Payable Days -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-emerald-500"></div>
                                                    <span
                                                        class="text-sm font-semibold text-slate-700 dark:text-navy-100">Payable
                                                        Days</span>
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-navy-100"
                                                    id="payable-count">0</span>
                                            </div>

                                            <!-- Total Non-Payable Days -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="size-2 rounded-full bg-red-500"></div>
                                                    <span
                                                        class="text-sm font-semibold text-slate-700 dark:text-navy-100">Non-Payable
                                                        Days</span>
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-navy-100"
                                                    id="non-payable-count">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code Generator -->
                                <div class="card mt-4">
                                    <div class="px-4 py-4 sm:px-5">
                                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                            QR Code Generator
                                        </h2>
                                    </div>
                                    <div class="px-4 pb-4 sm:px-5">
                                        <div class="text-center">
                                            <div id="qr-code-container" class="mb-4">
                                                <!-- QR Code will be generated here -->
                                            </div>
                                            <button onclick="generateQRCode()"
                                                class="btn bg-primary font-medium text-white hover:bg-primary-focus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v2" />
                                                </svg>
                                                Generate QR Code
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Management Tab -->
                    <div x-show="activeTab === 'leave-management'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Leave Management
                                </h2>
                                <a href="{{ route('leave-requests.create', ['program_id' => $program->id]) }}"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Request Leave
                                </a>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <!-- Leave Stats -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
                                    <div class="rounded-lg bg-yellow-100 p-4 dark:bg-yellow-900/20">
                                        <p class="text-xs+ text-yellow-800 dark:text-yellow-200">Pending</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $program->leaveRequests->where('status', 'pending')->count() }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-green-100 p-4 dark:bg-green-900/20">
                                        <p class="text-xs+ text-green-800 dark:text-green-200">Approved</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $program->leaveRequests->where('status', 'approved')->count() }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-red-100 p-4 dark:bg-red-900/20">
                                        <p class="text-xs+ text-red-800 dark:text-red-200">Rejected</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $program->leaveRequests->where('status', 'rejected')->count() }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-blue-100 p-4 dark:bg-blue-900/20">
                                        <p class="text-xs+ text-blue-800 dark:text-blue-200">Total Requests</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            {{ $program->leaveRequests->count() }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Leave Requests Table -->
                                @if($program->leaveRequests && $program->leaveRequests->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="border-b border-slate-200 dark:border-navy-500">
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Participant
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Leave Type
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Dates
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Duration
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Status
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                                @foreach($program->leaveRequests as $leaveRequest)
                                                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-600">
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <div class="flex items-center space-x-3">
                                                                <div class="avatar size-8">
                                                                    <div
                                                                        class="is-initial rounded-full bg-primary text-xs+ text-white">
                                                                        {{ substr($leaveRequest->user->first_name, 0, 1) }}{{ substr($leaveRequest->user->last_name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <p class="font-medium text-slate-700 dark:text-navy-100">
                                                                        {{ $leaveRequest->user->first_name }}
                                                                        {{ $leaveRequest->user->last_name }}
                                                                    </p>
                                                                    <p class="text-xs text-slate-500 dark:text-navy-300">
                                                                        {{ $leaveRequest->user->email }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span class="badge rounded-full bg-info text-white">
                                                                {{ ucfirst($leaveRequest->leave_type) }}
                                                            </span>
                                                            @if($leaveRequest->is_emergency)
                                                                <span class="ml-2 badge rounded-full bg-red-500 text-white text-xs">
                                                                    Emergency
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <div class="text-sm">
                                                                <p class="text-slate-700 dark:text-navy-100">
                                                                    {{ $leaveRequest->start_date->format('M d, Y') }}
                                                                </p>
                                                                <p class="text-slate-500 dark:text-navy-300">
                                                                    to {{ $leaveRequest->end_date->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span class="text-slate-600 dark:text-navy-200">
                                                                {{ $leaveRequest->duration }}
                                                                day{{ $leaveRequest->duration > 1 ? 's' : '' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <span
                                                                class="badge rounded-full {{ $leaveRequest->status_badge_class }}">
                                                                {{ ucfirst($leaveRequest->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 lg:px-5">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('leave-requests.show', $leaveRequest) }}"
                                                                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                                    <i class="fa fa-eye text-slate-500"></i>
                                                                </a>
                                                                @if($leaveRequest->status === 'pending')
                                                                    <button onclick="approveLeaveRequest({{ $leaveRequest->id }})"
                                                                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20"
                                                                        title="Approve">
                                                                        <i class="fa fa-check text-green-500"></i>
                                                                    </button>
                                                                    <button onclick="rejectLeaveRequest({{ $leaveRequest->id }})"
                                                                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20"
                                                                        title="Reject">
                                                                        <i class="fa fa-times text-red-500"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div class="text-slate-400 dark:text-navy-300 mb-2">
                                            <i class="fa fa-calendar-times text-4xl"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-navy-400">No leave requests yet</p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Participants can request
                                            leave for this program</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Budget Tab -->
                    <div x-show="activeTab === 'budget'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Budget Overview
                                </h2>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div class="rounded-lg bg-primary/10 p-4">
                                        <p class="text-xs+ text-primary">Daily Rate</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            R {{ number_format($program->daily_rate ?? 0, 2) }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-success/10 p-4">
                                        <p class="text-xs+ text-success">Transport Allowance</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            R {{ number_format($program->transport_allowance ?? 0, 2) }}
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-warning/10 p-4">
                                        <p class="text-xs+ text-warning">Total Value</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                            R
                                            {{ number_format($stats['total_value'] ?? $program->getTotalValue() ?? 0, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Tab -->
                    <div x-show="activeTab === 'compliance'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Compliance Settings
                                </h2>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div
                                        class="rounded-lg {{ $program->section_12h_eligible ? 'bg-success/10' : 'bg-slate-100 dark:bg-navy-600' }} p-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="flex size-8 items-center justify-center rounded-full {{ $program->section_12h_eligible ? 'bg-success' : 'bg-slate-400' }} text-white">
                                                <i
                                                    class="fa {{ $program->section_12h_eligible ? 'fa-check' : 'fa-times' }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">Section 12H</p>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                                    {{ $program->section_12h_eligible ? 'Eligible' : 'Not eligible' }}
                                                </p>
                                                @if($program->section_12h_eligible && $program->section_12h_contract_number)
                                                    <p class="text-xs text-slate-500 dark:text-navy-400">
                                                        Contract: {{ $program->section_12h_contract_number }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="rounded-lg {{ $program->eti_eligible_program ? 'bg-success/10' : 'bg-slate-100 dark:bg-navy-600' }} p-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="flex size-8 items-center justify-center rounded-full {{ $program->eti_eligible_program ? 'bg-success' : 'bg-slate-400' }} text-white">
                                                <i
                                                    class="fa {{ $program->eti_eligible_program ? 'fa-check' : 'fa-times' }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">ETI Eligible
                                                </p>
                                                <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                                    {{ $program->eti_eligible_program ? ($program->eti_category ? ucfirst($program->eti_category) : 'Eligible') : 'Not eligible' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SIM Cards Tab -->
                    <div x-show="activeTab === 'sim-cards'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    SIM Card Allocations
                                </h2>
                                <button onclick="openAllocationModal()"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Allocate SIM Card
                                </button>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <!-- SIM Card Stats -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
                                    <div class="rounded-lg bg-primary/10 p-4">
                                        <p class="text-xs+ text-primary">Total Allocated</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100" id="sim-total">
                                            0
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-success/10 p-4">
                                        <p class="text-xs+ text-success">Active</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100" id="sim-active">
                                            0
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-warning/10 p-4">
                                        <p class="text-xs+ text-warning">Returned</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100"
                                            id="sim-returned">
                                            0
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-info/10 p-4">
                                        <p class="text-xs+ text-info">Total Value</p>
                                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100" id="sim-value">
                                            R 0.00
                                        </p>
                                    </div>
                                </div>

                                <!-- SIM Card Allocations Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left" id="sim-allocations-table">
                                        <thead>
                                            <tr class="border-b border-slate-200 dark:border-navy-500">
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Learner
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Phone Number
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Serial Number
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Provider
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Allocated Date
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Status
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 dark:divide-navy-500"
                                            id="sim-allocations-tbody">
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center">
                                                    <div class="text-slate-400 dark:text-navy-300 mb-2">
                                                        <i class="fa fa-mobile-alt text-4xl"></i>
                                                    </div>
                                                    <p class="text-slate-500 dark:text-navy-400">No SIM cards allocated
                                                        yet</p>
                                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Start
                                                        allocating SIM cards to program learners</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div x-show="activeTab === 'documents'">
                        <div class="card">
                            <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                    Program Documents
                                </h2>
                                <button onclick="openDocumentUploadModal()"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Upload Document
                                </button>
                            </div>
                            <div class="px-4 pb-4 sm:px-5">
                                <div class="text-center py-8">
                                    <div class="text-slate-400 dark:text-navy-300 mb-2">
                                        <i class="fa fa-file-alt text-4xl"></i>
                                    </div>
                                    <p class="text-slate-500 dark:text-navy-400">No documents uploaded</p>
                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Upload contracts, reports,
                                        and other program documents</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Add CSRF token to head if it doesn't exist
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const csrfMeta = document.createElement('meta');
            csrfMeta.name = 'csrf-token';
            csrfMeta.content = '{{ csrf_token() }}';
            document.head.appendChild(csrfMeta);
        }

        function confirmDelete(programId) {
            if (confirm('Are you sure you want to delete this program? This action cannot be undone.')) {
                // Create a form to submit the DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('programs.destroy', $program) }}`;

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // SIM Card allocation functionality
        function openAllocationModal() {
            alert('SIM Card allocation modal will be implemented with seeders first');
        }

        // Add Participant functionality
        function openAddParticipantModal() {
            const userId = prompt('Enter User ID to add as participant:');
            if (userId) {
                addParticipant(userId);
            }
        }

        function addParticipant(userId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch(`{{ route('programs.add-participant', $program) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    status: 'active'
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Participant added successfully!');
                        location.reload(); // Refresh to show new participant
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add participant. Please try again.');
                });
        }

        // Edit Participant Status functionality
        function openEditParticipantModal(userId, currentStatus) {
            const newStatus = prompt(`Current status: ${currentStatus}\nEnter new status (active, inactive, completed, dropped):`, currentStatus);
            if (newStatus && newStatus !== currentStatus) {
                updateParticipantStatus(userId, newStatus);
            }
        }

        function updateParticipantStatus(userId, status) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch(`{{ route('programs.update-participant-status', [$program, '__USER_ID__']) }}`.replace('__USER_ID__', userId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Participant status updated successfully!');
                        location.reload(); // Refresh to show updated status
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update participant status. Please try again.');
                });
        }

        // Leave Request approval/rejection functionality
        function approveLeaveRequest(leaveRequestId) {
            if (confirm('Are you sure you want to approve this leave request?')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch(`/leave-requests/${leaveRequestId}/approve`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Leave request approved successfully!');
                            location.reload(); // Refresh to show updated status
                        } else {
                            alert('Error: ' + (data.message || 'Failed to approve leave request'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to approve leave request. Please try again.');
                    });
            }
        }

        function rejectLeaveRequest(leaveRequestId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch(`/leave-requests/${leaveRequestId}/reject`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rejection_reason: reason
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Leave request rejected successfully!');
                            location.reload(); // Refresh to show updated status
                        } else {
                            alert('Error: ' + (data.message || 'Failed to reject leave request'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to reject leave request. Please try again.');
                    });
            }
        }

        // Document Upload functionality
        function openDocumentUploadModal() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png';
            fileInput.onchange = function () {
                if (fileInput.files.length > 0) {
                    const documentType = prompt('Enter document type (e.g., Contract, Report, Certificate):');
                    const description = prompt('Enter document description (optional):');

                    if (documentType) {
                        uploadDocument(fileInput.files[0], documentType, description);
                    }
                }
            };
            fileInput.click();
        }

        function uploadDocument(file, documentType, description) {
            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', documentType);
            if (description) {
                formData.append('description', description);
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch(`{{ route('programs.upload-document', $program) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Document uploaded successfully!');
                        location.reload(); // Refresh to show new document
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to upload document. Please try again.');
                });
        }

        // Load SIM card allocations when the tab is activated
        document.addEventListener('DOMContentLoaded', function () {
            const simTab = document.getElementById('sim-cards-tab');
            if (simTab) {
                simTab.addEventListener('change', function () {
                    if (this.checked) {
                        loadSimAllocations();
                    }
                });
            }
        });

        function loadSimAllocations() {
            const programId = {{ $program->id }};

            // Add loading state
            const totalElement = document.getElementById('sim-total');
            const activeElement = document.getElementById('sim-active');
            const returnedElement = document.getElementById('sim-returned');
            const valueElement = document.getElementById('sim-value');

            totalElement.textContent = '...';
            activeElement.textContent = '...';
            returnedElement.textContent = '...';
            valueElement.textContent = 'R ...';

            fetch(`/programs/${programId}/sim-allocations`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.stats) {
                        updateSimStats(data.stats);
                    }
                    if (data.allocations) {
                        updateSimTable(data.allocations);
                    }
                })
                .catch(error => {
                    console.error('Error loading SIM allocations:', error);
                    // Reset to default values on error
                    totalElement.textContent = '0';
                    activeElement.textContent = '0';
                    returnedElement.textContent = '0';
                    valueElement.textContent = 'R 0.00';

                    // Show error message to user
                    const tbody = document.getElementById('sim-allocations-tbody');
                    if (tbody) {
                        tbody.innerHTML = `
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center">
                                                <div class="text-error mb-2">
                                                    <i class="fa fa-exclamation-triangle text-4xl"></i>
                                                </div>
                                                <p class="text-slate-500 dark:text-navy-400">Failed to load SIM allocations</p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Please try again later</p>
                                            </td>
                                        </tr>
                                    `;
                    }
                });
        }

        function updateSimStats(stats) {
            const totalElement = document.getElementById('sim-total');
            const activeElement = document.getElementById('sim-active');
            const returnedElement = document.getElementById('sim-returned');
            const valueElement = document.getElementById('sim-value');

            if (totalElement) totalElement.textContent = stats.total || '0';
            if (activeElement) activeElement.textContent = stats.active || '0';
            if (returnedElement) returnedElement.textContent = stats.returned || '0';
            if (valueElement) valueElement.textContent = `R ${(stats.total_value || 0).toFixed(2)}`;
        }

        function updateSimTable(allocations) {
            const tbody = document.getElementById('sim-allocations-tbody');

            if (allocations.length === 0) {
                tbody.innerHTML = `
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center">
                                                    <div class="text-slate-400 dark:text-navy-300 mb-2">
                                                        <i class="fa fa-mobile-alt text-4xl"></i>
                                                    </div>
                                                    <p class="text-slate-500 dark:text-navy-400">No SIM cards allocated yet</p>
                                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Start allocating SIM cards to program learners</p>
                                                </td>
                                            </tr>
                                        `;
                return;
            }

            tbody.innerHTML = allocations.map(allocation => `
                                        <tr class="border-transparent hover:border-slate-200 dark:hover:border-navy-500">
                                            <td class="px-4 py-3 lg:px-5">
                                                <div class="font-medium text-slate-700 dark:text-navy-100">
                                                    ${allocation.user.first_name} ${allocation.user.last_name}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    ${allocation.sim_card.phone_number}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="text-slate-600 dark:text-navy-200">
                                                    ${allocation.sim_card.serial_number}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="text-slate-600 dark:text-navy-200">
                                                    ${allocation.sim_card.service_provider}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="text-slate-600 dark:text-navy-200">
                                                    ${new Date(allocation.allocated_date).toLocaleDateString()}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="badge rounded-full ${allocation.status === 'active' ? 'bg-success' :
                    allocation.status === 'returned' ? 'bg-info' :
                        'bg-warning'
                } text-white">
                                                    ${allocation.status.charAt(0).toUpperCase() + allocation.status.slice(1)}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                ${allocation.status === 'active' ?
                    `<button onclick="returnSimCard(${allocation.id})" class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                        <i class="fa fa-undo text-slate-500"></i>
                                                    </button>` :
                    '<span class="text-slate-400">-</span>'
                }
                                            </td>
                                        </tr>
                                    `).join('');
        }

        function returnSimCard(allocationId) {
            if (!confirm('Are you sure you want to return this SIM card?')) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch(`/sim-card-allocations/${allocationId}/return`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Reload the SIM allocations to reflect the change
                        loadSimAllocations();
                        alert('SIM card returned successfully');
                    } else {
                        alert('Failed to return SIM card: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error returning SIM card:', error);
                    alert('Failed to return SIM card. Please try again.');
                });
        }

        // Schedule Management Functions
        let selectedScheduleId = null;
        let schedulesData = @json($schedules ?? []);

        function selectSchedule(scheduleId) {
            selectedScheduleId = scheduleId;

            // Remove previous selection
            document.querySelectorAll('.schedule-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-primary', 'bg-primary/5');
            });

            // Highlight selected card
            const selectedCard = document.querySelector(`[data-schedule-id="${scheduleId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('ring-2', 'ring-primary', 'bg-primary/5');
            }

            // Show details and actions
            showScheduleDetails(scheduleId);
        }

        function showScheduleDetails(scheduleId) {
            const schedule = schedulesData.find(s => s.id === scheduleId);
            if (!schedule) return;

            const detailsCard = document.getElementById('schedule-details-card');
            const actionsCard = document.getElementById('attendance-actions-card');
            const detailsContent = document.getElementById('schedule-details-content');

            // Populate details
            detailsContent.innerHTML = `
                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-semibold text-slate-700 dark:text-navy-100">${schedule.title || 'Training Session'}</h3>
                                    <p class="text-sm text-slate-500 dark:text-navy-300">${schedule.session_date ? new Date(schedule.session_date).toLocaleDateString() : 'No date set'}</p>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Time:</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">${schedule.start_time || '09:00'} - ${schedule.end_time || '17:00'}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Type:</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">${schedule.session_type ? schedule.session_type.charAt(0).toUpperCase() + schedule.session_type.slice(1) : 'Lecture'}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Duration:</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">${schedule.planned_duration_hours || 0} hours</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Status:</span>
                                        <span class="badge bg-${schedule.status === 'completed' ? 'success' : (schedule.status === 'in_progress' ? 'warning' : 'primary')} text-white">
                                            ${schedule.status ? schedule.status.charAt(0).toUpperCase() + schedule.status.slice(1) : 'Scheduled'}
                                        </span>
                                    </div>

                                    ${schedule.is_online ? `
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Mode:</span>
                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Online</span>
                                    </div>
                                    ` : ''}

                                    ${schedule.location ? `
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Location:</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">${schedule.location}</span>
                                    </div>
                                    ` : ''}

                                    ${schedule.description ? `
                                    <div>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Description:</span>
                                        <p class="text-sm text-slate-700 dark:text-navy-100 mt-1">${schedule.description}</p>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;

            // Show cards
            detailsCard.style.display = 'block';
            actionsCard.style.display = 'block';
        }

        function viewSchedule(scheduleId) {
            // Open schedule view modal or redirect to schedule details
            window.open(`/schedules/${scheduleId}`, '_blank');
        }

        function editSchedule(scheduleId) {
            // Open schedule edit modal or redirect to edit page
            window.location.href = `/schedules/${scheduleId}/edit`;
        }

        function editSelectedSchedule() {
            if (selectedScheduleId) {
                editSchedule(selectedScheduleId);
            }
        }

        function markAttendanceForSchedule() {
            if (selectedScheduleId) {
                // Open attendance marking modal for the selected schedule
                openAttendanceModal(selectedScheduleId);
            }
        }

        function viewAttendanceForSchedule() {
            if (selectedScheduleId) {
                // Open attendance view for the selected schedule
                window.open(`/attendance/schedule/${selectedScheduleId}`, '_blank');
            }
        }

        function generateScheduleQR() {
            if (selectedScheduleId) {
                // Generate QR code for the selected schedule
                generateQRCode(selectedScheduleId);
            }
        }

        function openScheduleModal() {
            // Open create schedule modal
            window.location.href = `/schedules/create?program_id={{ $program->id }}`;
        }

        function openAttendanceModal(scheduleId = null) {
            // Open attendance marking modal
            if (scheduleId) {
                window.open(`/attendance/schedule/${scheduleId}`, '_blank');
            } else {
                window.open(`/attendance/create?program_id={{ $program->id }}`, '_blank');
            }
        }

        function openBulkAttendanceModal() {
            // Open bulk attendance marking modal
            window.open(`/attendance/bulk?program_id={{ $program->id }}`, '_blank');
        }

        function generateQRCode(scheduleId = null) {
            const qrContainer = document.getElementById('qr-code-container');
            if (!qrContainer) return;

            const qrData = scheduleId ?
                `schedule:${scheduleId}:{{ $program->id }}` :
                `program:{{ $program->id }}`;

            // Simple QR code generation (you might want to use a proper QR library)
            qrContainer.innerHTML = `
                            <div class="bg-white p-4 rounded-lg border-2 border-dashed border-slate-300">
                                <div class="text-center">
                                    <div class="w-32 h-32 mx-auto bg-slate-100 rounded flex items-center justify-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v2" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500">QR Code for ${scheduleId ? 'Schedule' : 'Program'}</p>
                                    <p class="text-xs text-slate-400 font-mono">${qrData}</p>
                                </div>
                            </div>
                        `;
        }

        function filterSchedules() {
            const statusFilter = document.getElementById('schedule-status-filter').value;
            const dateFilter = document.getElementById('schedule-date-filter').value;
            const typeFilter = document.getElementById('schedule-type-filter').value;

            const cards = document.querySelectorAll('.schedule-card');

            cards.forEach(card => {
                const scheduleId = card.dataset.scheduleId;
                const schedule = schedulesData.find(s => s.id == scheduleId);

                let show = true;

                if (statusFilter && schedule.status !== statusFilter) {
                    show = false;
                }

                if (dateFilter && schedule.session_date) {
                    const scheduleDate = new Date(schedule.session_date).toISOString().split('T')[0];
                    if (scheduleDate !== dateFilter) {
                        show = false;
                    }
                }

                if (typeFilter && schedule.session_type !== typeFilter) {
                    show = false;
                }

                card.style.display = show ? 'block' : 'none';
            });
        }

        function filterAttendance() {
            // This would filter attendance records based on the selected filters
            console.log('Filtering attendance records...');
        }

        // Initialize tab functionality and load default content
        document.addEventListener('DOMContentLoaded', function () {
            // Ensure overview tab is selected by default
            const overviewTab = document.getElementById('overview-tab');
            if (overviewTab) {
                overviewTab.checked = true;
            }
        });
    </script>
@endsection