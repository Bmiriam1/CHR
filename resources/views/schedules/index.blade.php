@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Training Schedules
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Manage training sessions and schedules
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('schedules.calendar') }}"
                        class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                        <i class="fa fa-calendar mr-2"></i>
                        Calendar View
                    </a>
                    @can('create', App\Models\Schedule::class)
                        <a href="{{ route('schedules.create') }}"
                            class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent-focus/90">
                            <i class="fa fa-plus mr-2"></i>
                            Create Schedule
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Total Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Sessions</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['total']) }}
                            </h3>
                            <p class="text-xs text-info">All Time</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-calendar text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Today's Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Today's Sessions</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['today']) }}
                            </h3>
                            <p class="text-xs {{ $stats['today'] > 0 ? 'text-success' : 'text-slate-400' }}">
                                {{ $stats['today'] > 0 ? 'Active' : 'None' }}</p>
                        </div>
                        <div
                            class="mask is-squircle flex size-10 items-center justify-center {{ $stats['today'] > 0 ? 'bg-success/10' : 'bg-slate-100 dark:bg-navy-500' }}">
                            <i
                                class="fa fa-clock {{ $stats['today'] > 0 ? 'text-success' : 'text-slate-400 dark:text-navy-300' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Scheduled</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['scheduled']) }}
                            </h3>
                            <p class="text-xs text-primary">Upcoming</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-calendar-check text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Completed</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['completed']) }}
                            </h3>
                            <p class="text-xs text-success">Finished</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- This Week -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">This Week</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['this_week']) }}
                            </h3>
                            <p class="text-xs text-warning">Sessions</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-calendar-week text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Online Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Online Sessions</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['online_sessions']) }}
                            </h3>
                            <p class="text-xs text-info">Virtual</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-video text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Attendees -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Attendees</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['total_attendees']) }}
                            </h3>
                            <p class="text-xs text-secondary">All Sessions</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                            <i class="fa fa-users text-secondary"></i>
                        </div>
                    </div>
                </div>

                <!-- Average Attendance -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Avg Attendance</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($stats['avg_attendance_rate'], 1) }}%
                            </h3>
                            <p
                                class="text-xs {{ $stats['avg_attendance_rate'] >= 80 ? 'text-success' : ($stats['avg_attendance_rate'] >= 60 ? 'text-warning' : 'text-error') }}">
                                Rate</p>
                        </div>
                        <div
                            class="mask is-squircle flex size-10 items-center justify-center {{ $stats['avg_attendance_rate'] >= 80 ? 'bg-success/10' : ($stats['avg_attendance_rate'] >= 60 ? 'bg-warning/10' : 'bg-error/10') }}">
                            <i
                                class="fa fa-chart-bar {{ $stats['avg_attendance_rate'] >= 80 ? 'text-success' : ($stats['avg_attendance_rate'] >= 60 ? 'text-warning' : 'text-error') }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 card">
                <div class="px-4 py-4 sm:px-5">
                    <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100 mb-4">
                        Filter Schedules
                    </h3>
                    <form method="GET" action="{{ route('schedules.index') }}" class="flex flex-wrap items-end gap-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-48">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Search</label>
                            <input name="search" value="{{ request('search') }}"
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                placeholder="Search sessions, modules, programs..." />
                        </div>

                        <!-- Program Filter -->
                        <div class="min-w-48">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Program</label>
                            <select name="program_id"
                                class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="min-w-32">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Status</label>
                            <select name="status"
                                class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Date From Filter -->
                        <div class="min-w-36">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'program_id', 'status', 'date_from']))
                                <a href="{{ route('schedules.index') }}" class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Schedules Table -->
            <div class="mt-6 card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-navy-500">
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Session
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Program
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Date & Time
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Instructor
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Attendance
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Status
                                </th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                            @forelse($schedules as $schedule)
                                <tr class="border-transparent hover:border-slate-200 dark:hover:border-navy-500">
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex size-8 items-center justify-center rounded-full text-xs+ font-medium text-white"
                                                 style="background-color: {{ $schedule->getStatusColor() === 'blue' ? '#3b82f6' : ($schedule->getStatusColor() === 'green' ? '#10b981' : ($schedule->getStatusColor() === 'yellow' ? '#f59e0b' : ($schedule->getStatusColor() === 'red' ? '#ef4444' : '#6b7280'))) }}">
                                                <i class="fa fa-{{ $schedule->is_online ? 'video' : 'chalkboard-teacher' }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $schedule->title }}
                                                </p>
                                                @if($schedule->module_name)
                                                    <p class="text-xs text-slate-400 dark:text-navy-300">
                                                        {{ $schedule->module_name }}
                                                    </p>
                                                @endif
                                                @if($schedule->is_online)
                                                    <span class="rounded bg-info/10 px-2 py-1 text-xs font-medium text-info">
                                                        Online
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($schedule->program)
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $schedule->program->title }}
                                                </p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $schedule->program->program_code }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-navy-300">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ \Carbon\Carbon::parse($schedule->session_date)->format('M j, Y') }}
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($schedule->instructor)
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $schedule->instructor->first_name }} {{ $schedule->instructor->last_name }}
                                                </p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $schedule->instructor->email }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-navy-300">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                {{ $schedule->actual_attendees ?? 0 }} / {{ $schedule->expected_attendees }}
                                            </span>
                                        </div>
                                        @if($schedule->attendance_rate)
                                            <div class="mt-1">
                                                <div class="h-1.5 w-full rounded-full bg-slate-200 dark:bg-navy-500">
                                                    <div class="h-1.5 rounded-full bg-primary" style="width: {{ $schedule->attendance_rate }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($schedule->status === 'scheduled')
                                            <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs+ font-medium text-primary">
                                                Scheduled
                                            </span>
                                        @elseif($schedule->status === 'in_progress')
                                            <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                                In Progress
                                            </span>
                                        @elseif($schedule->status === 'completed')
                                            <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                                Completed
                                            </span>
                                        @elseif($schedule->status === 'cancelled')
                                            <span class="rounded-full bg-error/10 px-2.5 py-1 text-xs+ font-medium text-error">
                                                Cancelled
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs+ font-medium text-slate-500 dark:bg-navy-500 dark:text-navy-100">
                                                {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-2">
                                            @can('view', $schedule)
                                                <a href="{{ route('schedules.show', $schedule) }}" 
                                                   class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                    <i class="fa-solid fa-eye text-slate-500 dark:text-navy-300"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('update', $schedule)
                                                <a href="{{ route('schedules.edit', $schedule) }}" 
                                                   class="btn size-8 rounded-full p-0 hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25">
                                                    <i class="fa-solid fa-edit text-warning"></i>
                                                </a>
                                            @endcan

                                            @can('manage', $schedule)
                                                @if($schedule->status === 'scheduled')
                                                    <form action="{{ route('schedules.start', $schedule) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn size-8 rounded-full p-0 hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                                title="Start Session">
                                                            <i class="fa-solid fa-play text-success"></i>
                                                        </button>
                                                    </form>
                                                @elseif($schedule->status === 'in_progress')
                                                    <form action="{{ route('schedules.complete', $schedule) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn size-8 rounded-full p-0 hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                                                title="Complete Session">
                                                            <i class="fa-solid fa-check text-info"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan

                                            @if($schedule->qr_code_active && $schedule->status !== 'cancelled')
                                                <a href="{{ route('schedules.qr-code', $schedule) }}" target="_blank"
                                                   class="btn size-8 rounded-full p-0 hover:bg-secondary/20 focus:bg-secondary/20 active:bg-secondary/25">
                                                    <i class="fa-solid fa-qrcode text-secondary"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 dark:text-navy-400"></i>
                                            <p class="text-slate-600 dark:text-navy-200">No schedules found</p>
                                            @if(request()->hasAny(['search', 'program_id', 'status', 'date_from']))
                                                <a href="{{ route('schedules.index') }}" class="text-primary hover:text-primary-focus">
                                                    Clear filters
                                                </a>
                                            @else
                                                @can('create', App\Models\Schedule::class)
                                                    <a href="{{ route('schedules.create') }}"
                                                        class="btn bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                                        <i class="fa fa-plus mr-2"></i>
                                                        Create Schedule
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($schedules->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                        {{ $schedules->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection