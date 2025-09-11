@extends('layouts.app')

@section('content')
<style>
/* Tab CSS */
.tabs {
    position: relative;
}

.tab-switch {
    display: none;
}

.tabs-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    border-bottom: 1px solid;
    border-color: var(--color-slate-200);
    margin-bottom: 1rem;
}

.tabs-list:where(.dark, .dark *) {
    border-color: var(--color-navy-500);
}

.tab {
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.15s ease;
    color: var(--color-slate-500);
}

.tab:where(.dark, .dark *) {
    color: var(--color-navy-300);
}

.tab:hover {
    color: var(--color-slate-700);
    border-bottom-color: var(--color-slate-300);
}

.tab:hover:where(.dark, .dark *) {
    color: var(--color-navy-100);
    border-bottom-color: var(--color-navy-400);
}

/* Active tab styles */
.tab-switch:checked + * .tab,
.tab-switch:checked + * + * .tab,
.tab-switch:checked + * + * + * .tab,
.tab-switch:checked + * + * + * + * .tab,
.tab-switch:checked + * + * + * + * + * .tab,
.tab-switch:checked + * + * + * + * + * + * .tab,
.tab-switch:checked + * + * + * + * + * + * + * .tab,
.tab-switch:checked + * + * + * + * + * + * + * + * .tab {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}

/* Tab content */
.tab-content {
    width: 100%;
}

.tab-pane {
    display: none;
}

/* Show active tab content */
#overview-tab:checked ~ .tab-content .tab-pane:nth-child(1),
#schedules-tab:checked ~ .tab-content .tab-pane:nth-child(2),
#participants-tab:checked ~ .tab-content .tab-pane:nth-child(3),
#leave-management-tab:checked ~ .tab-content .tab-pane:nth-child(4),
#budget-tab:checked ~ .tab-content .tab-pane:nth-child(5),
#compliance-tab:checked ~ .tab-content .tab-pane:nth-child(6),
#sim-cards-tab:checked ~ .tab-content .tab-pane:nth-child(7),
#documents-tab:checked ~ .tab-content .tab-pane:nth-child(8) {
    display: block;
}

/* Active tab label styles */
#overview-tab:checked ~ .tabs-list label[for="overview-tab"],
#schedules-tab:checked ~ .tabs-list label[for="schedules-tab"],
#participants-tab:checked ~ .tabs-list label[for="participants-tab"],
#leave-management-tab:checked ~ .tabs-list label[for="leave-management-tab"],
#budget-tab:checked ~ .tabs-list label[for="budget-tab"],
#compliance-tab:checked ~ .tabs-list label[for="compliance-tab"],
#sim-cards-tab:checked ~ .tabs-list label[for="sim-cards-tab"],
#documents-tab:checked ~ .tabs-list label[for="documents-tab"] {
    color: var(--color-primary) !important;
    border-bottom-color: var(--color-primary) !important;
}
</style>
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
            <div class="mt-8">
                <div class="tabs flex flex-wrap">
                    <input class="tab-switch" id="overview-tab" name="tabs" type="radio" checked="">
                    <input class="tab-switch" id="schedules-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="participants-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="leave-management-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="budget-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="compliance-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="sim-cards-tab" name="tabs" type="radio">
                    <input class="tab-switch" id="documents-tab" name="tabs" type="radio">

                    <div class="tabs-list">
                        <label for="overview-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Overview
                        </label>
                        <label for="schedules-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Schedule
                        </label>
                        <label for="participants-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            Participants
                        </label>
                        <label for="leave-management-tab"
                            class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Leave Management
                        </label>
                        <label for="budget-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Budget
                        </label>
                        <label for="compliance-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Compliance
                        </label>
                        <label for="sim-cards-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            SIM Cards
                        </label>
                        <label for="documents-tab" class="tab btn shrink-0 rounded-lg px-3 py-1.5 text-xs+ font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Documents
                        </label>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane mt-4">
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
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Manage Schedule
                                                </a>
                                                <a href="{{ route('programs.progress', $program) }}"
                                                    class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    View Progress
                                                </a>
                                                <a href="{{ route('programs.revenue-report', $program) }}"
                                                    class="btn w-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    Revenue Reports
                                                </a>
                                                <a href="{{ route('programs.client-pack', $program) }}"
                                                    class="btn w-full bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
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
                        <div class="tab-pane mt-4">
                            <div class="card">
                                <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                                    <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                        Training Schedule
                                    </h2>
                                    <a href="{{ route('schedules.create', ['program_id' => $program->id]) }}"
                                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Schedule
                                    </a>
                                </div>
                                <div class="px-4 pb-4 sm:px-5">
                                    @if(isset($schedules) && $schedules->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($schedules as $schedule)
                                                <div
                                                    class="flex items-center justify-between rounded-lg bg-slate-100 p-3 dark:bg-navy-600">
                                                    <div>
                                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                                            {{ $schedule->date ? $schedule->date->format('M d, Y') : 'No date set' }}
                                                        </p>
                                                        <p class="text-sm text-slate-500 dark:text-navy-300">
                                                            {{ $schedule->start_time ?? '09:00' }} -
                                                            {{ $schedule->end_time ?? '17:00' }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="badge bg-{{ $schedule->status === 'completed' ? 'success' : 'primary' }} text-white">
                                                        {{ ucfirst($schedule->status ?? 'scheduled') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div class="text-slate-400 dark:text-navy-300 mb-2">
                                                <i class="fa fa-calendar-alt text-4xl"></i>
                                            </div>
                                            <p class="text-slate-500 dark:text-navy-400">No training sessions scheduled</p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Create your first training
                                                session</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Participants Tab -->
                        <div class="tab-pane mt-4">
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
                                                                <span
                                                                    class="badge rounded-full {{ $participant->status_badge_class }}">
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
                                                                    <button onclick="openEditParticipantModal({{ $participant->user->id }}, '{{ $participant->status }}')"
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

                        <!-- Leave Management Tab -->
                        <div class="tab-pane mt-4">
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
                                                                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20" title="Approve">
                                                                            <i class="fa fa-check text-green-500"></i>
                                                                        </button>
                                                                        <button onclick="rejectLeaveRequest({{ $leaveRequest->id }})"
                                                                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20" title="Reject">
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
                        <div class="tab-pane mt-4">
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
                        <div class="tab-pane mt-4">
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
                        <div class="tab-pane mt-4">
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
                                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100"
                                                id="sim-total">
                                                0
                                            </p>
                                        </div>
                                        <div class="rounded-lg bg-success/10 p-4">
                                            <p class="text-xs+ text-success">Active</p>
                                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100"
                                                id="sim-active">
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
                                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100"
                                                id="sim-value">
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
                        <div class="tab-pane mt-4">
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
            fileInput.onchange = function() {
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

        // Initialize tab functionality and load default content
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure overview tab is selected by default
            const overviewTab = document.getElementById('overview-tab');
            if (overviewTab) {
                overviewTab.checked = true;
            }
        });
    </script>
@endsection