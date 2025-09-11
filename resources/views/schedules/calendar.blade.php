@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Training Calendar
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        View training sessions in calendar format
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('schedules.index') }}"
                        class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                        <i class="fa fa-list mr-2"></i>
                        List View
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

            <!-- Monthly Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-5">
                <!-- Total Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Sessions</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($monthlyStats['total_sessions']) }}
                            </h3>
                            <p class="text-xs text-info">This Month</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-calendar text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Completed</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($monthlyStats['completed_sessions']) }}
                            </h3>
                            <p class="text-xs text-success">Finished</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Sessions -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Scheduled</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($monthlyStats['scheduled_sessions']) }}
                            </h3>
                            <p class="text-xs text-primary">Upcoming</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-calendar-check text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Attendees -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Attendees</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ number_format($monthlyStats['total_attendees']) }}
                            </h3>
                            <p class="text-xs text-secondary">This Month</p>
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
                                {{ number_format($monthlyStats['avg_attendance_rate'], 1) }}%
                            </h3>
                            <p
                                class="text-xs {{ $monthlyStats['avg_attendance_rate'] >= 80 ? 'text-success' : ($monthlyStats['avg_attendance_rate'] >= 60 ? 'text-warning' : 'text-error') }}">
                                Rate</p>
                        </div>
                        <div
                            class="mask is-squircle flex size-10 items-center justify-center {{ $monthlyStats['avg_attendance_rate'] >= 80 ? 'bg-success/10' : ($monthlyStats['avg_attendance_rate'] >= 60 ? 'bg-warning/10' : 'bg-error/10') }}">
                            <i
                                class="fa fa-chart-bar {{ $monthlyStats['avg_attendance_rate'] >= 80 ? 'text-success' : ($monthlyStats['avg_attendance_rate'] >= 60 ? 'text-warning' : 'text-error') }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Navigation -->
            <div class="mt-6 card">
                <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                        </h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Previous Month -->
                        @php
                            $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                        @endphp
                        <a href="{{ route('schedules.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                            <i class="fa-solid fa-chevron-left text-base"></i>
                        </a>

                        <!-- Today Button -->
                        <a href="{{ route('schedules.calendar') }}"
                            class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200 dark:bg-navy-500 dark:text-navy-100 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450">
                            Today
                        </a>

                        <!-- Next Month -->
                        @php
                            $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
                        @endphp
                        <a href="{{ route('schedules.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                            <i class="fa-solid fa-chevron-right text-base"></i>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="px-4 pb-4 sm:px-5">
                    @php
                        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
                        $endOfMonth = $startOfMonth->copy()->endOfMonth();
                        $startDate = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $endDate = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                        $currentDate = $startDate->copy();
                    @endphp

                    <!-- Calendar Header -->
                    <div class="grid grid-cols-7 gap-px mb-2">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="p-2 text-center text-xs font-medium text-slate-500 dark:text-navy-300">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Calendar Body -->
                    <div class="grid grid-cols-7 gap-px bg-slate-200 dark:bg-navy-500 rounded-lg overflow-hidden">
                        @while($currentDate->lte($endDate))
                            @php
                                $dateKey = $currentDate->format('Y-m-d');
                                $daySchedules = $schedules->get($dateKey, collect());
                                $isCurrentMonth = $currentDate->month === $month;
                                $isToday = $currentDate->isToday();
                            @endphp

                            <div class="min-h-[120px] bg-white dark:bg-navy-700 p-2 {{ !$isCurrentMonth ? 'opacity-50' : '' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-sm font-medium {{ $isToday ? 'text-primary' : 'text-slate-700 dark:text-navy-100' }}">
                                        {{ $currentDate->day }}
                                    </span>
                                    @if($isToday)
                                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                                    @endif
                                </div>

                                @if($daySchedules->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($daySchedules->take(3) as $schedule)
                                            <div class="text-xs p-1 rounded cursor-pointer hover:opacity-80
                                                                {{ $schedule->status === 'scheduled' ? 'bg-primary/10 text-primary' : '' }}
                                                                {{ $schedule->status === 'in_progress' ? 'bg-warning/10 text-warning' : '' }}
                                                                {{ $schedule->status === 'completed' ? 'bg-success/10 text-success' : '' }}
                                                                {{ $schedule->status === 'cancelled' ? 'bg-error/10 text-error' : '' }}"
                                                onclick="showScheduleDetails({{ $schedule->id }})" title="{{ $schedule->title }}">
                                                <div class="truncate font-medium">{{ $schedule->title }}</div>
                                                <div class="truncate opacity-75">
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                                                </div>
                                            </div>
                                        @endforeach

                                        @if($daySchedules->count() > 3)
                                            <div class="text-xs text-slate-500 dark:text-navy-300 font-medium">
                                                +{{ $daySchedules->count() - 3 }} more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @php $currentDate->addDay(); @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div id="scheduleModal"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 opacity-0 hidden"
        style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>
        <div class="relative w-full max-w-2xl origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700">
            <div class="flex items-center justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                    Schedule Details
                </h3>
                <button onclick="closeScheduleModal()"
                    class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div id="scheduleModalContent" class="px-4 py-4 sm:px-5">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script>
        function showScheduleDetails(scheduleId) {
            // You would typically make an AJAX call here to fetch schedule details
            // For now, we'll redirect to the schedule show page
            window.location.href = `/schedules/${scheduleId}`;
        }

        function closeScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    </script>
@endsection