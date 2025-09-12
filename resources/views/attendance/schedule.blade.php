@extends('layouts.app')

@section('title', 'Schedule Attendance')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between space-x-2 mb-6">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                        Schedule Attendance
                    </h2>
                    <p class="text-slate-500 dark:text-navy-200 text-sm">
                        Manage attendance for {{ $schedule->title ?? 'Training Session' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('programs.show', $schedule->program_id) }}" 
                       class="btn bg-slate-150 text-slate-800 hover:bg-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Program
                    </a>
                    <button onclick="markAllPresent()" 
                            class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        Mark All Present
                    </button>
                </div>
            </div>

            <!-- Schedule Information Cards -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <div class="card">
                    <div class="flex items-center justify-between space-x-1">
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="is-initial rounded-full bg-info text-xs+ text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs+ text-slate-600 dark:text-navy-300">Session Title</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-navy-100">{{ $schedule->title ?? 'Training Session' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between space-x-1">
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="is-initial rounded-full bg-warning text-xs+ text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs+ text-slate-600 dark:text-navy-300">Date & Time</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                    {{ $schedule->session_date ? $schedule->session_date->format('M d, Y') : 'Not set' }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-navy-300">
                                    {{ $schedule->start_time ?? '09:00' }} - {{ $schedule->end_time ?? '17:00' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between space-x-1">
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="is-initial rounded-full bg-primary text-xs+ text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs+ text-slate-600 dark:text-navy-300">Status</p>
                                <span class="badge bg-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'in_progress' ? 'warning' : 'primary') }} text-white text-xs">
                                    {{ ucfirst($schedule->status ?? 'scheduled') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between space-x-1">
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="is-initial rounded-full bg-success text-xs+ text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs+ text-slate-600 dark:text-navy-300">Participants</p>
                                <p class="text-sm font-semibold text-slate-700 dark:text-navy-100">{{ count($attendanceData) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Management -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h3 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Program Participants
                        </h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-slate-500 dark:text-navy-300">
                                {{ count($attendanceData) }} participants
                            </span>
                            <button onclick="saveAllAttendance()" 
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Save All
                            </button>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <form id="attendance-form" method="POST" action="{{ route('attendance.bulk-mark') }}">
                            @csrf
                            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                            <input type="hidden" name="program_id" value="{{ $schedule->program_id }}">
                            <input type="hidden" name="attendance_date" value="{{ $schedule->session_date }}">
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-y border-slate-200 dark:border-navy-500">
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Participant
                                            </th>
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Status
                                            </th>
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Check In/Out
                                            </th>
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Hours
                                            </th>
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Payable
                                            </th>
                                            <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                        @forelse($attendanceData as $index => $data)
                                            @php
                                                $user = $data['user'];
                                                $attendance = $data['attendance'];
                                                $isExisting = $attendance->exists;
                                            @endphp
                                            <tr class="hover:bg-slate-50 dark:hover:bg-navy-600">
                                                <td class="px-4 py-3 lg:px-5">
                                                    <div class="flex items-center">
                                                        <div class="avatar size-10">
                                                            <img class="rounded-full" src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="avatar">
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $user->name }}</p>
                                                            <p class="text-sm text-slate-500 dark:text-navy-300">{{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 lg:px-5">
                                                    <select name="attendance[{{ $index }}][status]" 
                                                            class="form-select text-xs" 
                                                            onchange="updateAttendanceStatus({{ $index }})">
                                                        <option value="present" {{ $attendance->status === 'present' ? 'selected' : '' }}>Present</option>
                                                        <option value="late" {{ $attendance->status === 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="absent_unauthorized" {{ $attendance->status === 'absent_unauthorized' ? 'selected' : '' }}>Absent (Unauthorized)</option>
                                                        <option value="absent_authorized" {{ $attendance->status === 'absent_authorized' ? 'selected' : '' }}>Absent (Authorized)</option>
                                                        <option value="excused" {{ $attendance->status === 'excused' ? 'selected' : '' }}>Excused</option>
                                                        <option value="on_leave" {{ $attendance->status === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                                        <option value="sick" {{ $attendance->status === 'sick' ? 'selected' : '' }}>Sick</option>
                                                        <option value="half_day" {{ $attendance->status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                    </select>
                                                    <input type="hidden" name="attendance[{{ $index }}][user_id]" value="{{ $user->id }}">
                                                    @if($isExisting)
                                                        <input type="hidden" name="attendance[{{ $index }}][attendance_id]" value="{{ $attendance->id }}">
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 lg:px-5">
                                                    <div class="space-y-1">
                                                        <input type="time" 
                                                               name="attendance[{{ $index }}][check_in_time]" 
                                                               value="{{ $attendance->check_in_time }}" 
                                                               class="form-input text-xs" 
                                                               placeholder="Check In">
                                                        <input type="time" 
                                                               name="attendance[{{ $index }}][check_out_time]" 
                                                               value="{{ $attendance->check_out_time }}" 
                                                               class="form-input text-xs" 
                                                               placeholder="Check Out">
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 lg:px-5">
                                                    <input type="number" 
                                                           name="attendance[{{ $index }}][hours_worked]" 
                                                           value="{{ $attendance->hours_worked }}" 
                                                           step="0.5" 
                                                           min="0" 
                                                           max="24" 
                                                           class="form-input text-xs" 
                                                           placeholder="Hours">
                                                </td>
                                                <td class="px-4 py-3 lg:px-5">
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" 
                                                               name="attendance[{{ $index }}][is_payable]" 
                                                               value="1" 
                                                               {{ $attendance->is_payable ? 'checked' : '' }} 
                                                               class="form-checkbox">
                                                        <span class="ml-2 text-sm text-slate-600 dark:text-navy-200">Payable</span>
                                                    </label>
                                                </td>
                                                <td class="px-4 py-3 lg:px-5">
                                                    <div class="flex space-x-1">
                                                        <button type="button" 
                                                                onclick="markPresent({{ $index }})" 
                                                                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                                            </svg>
                                                        </button>
                                                        <button type="button" 
                                                                onclick="markAbsent({{ $index }})" 
                                                                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-8 text-center">
                                                    <div class="text-slate-400 dark:text-navy-300 mb-2">
                                                        <i class="fa fa-users text-4xl"></i>
                                                    </div>
                                                    <p class="text-slate-500 dark:text-navy-400">No participants found for this program</p>
                                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Add participants to the program first</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary Cards -->
            <div class="mt-6">
                <div class="grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-8">
                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-success text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Present</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="present-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-warning text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Late</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="late-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-error text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Absent (Unauth)</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="absent-unauth-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-info text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Absent (Auth)</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="absent-auth-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-purple-500 text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Excused</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="excused-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-indigo-500 text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">On Leave</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="on-leave-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-orange-500 text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Sick</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="sick-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between space-x-1">
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="is-initial rounded-full bg-emerald-500 text-xs+ text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs+ text-slate-600 dark:text-navy-300">Payable</p>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-navy-100" id="payable-count">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markPresent(index) {
            const statusSelect = document.querySelector(`select[name="attendance[${index}][status]"]`);
            const payableCheckbox = document.querySelector(`input[name="attendance[${index}][is_payable]"]`);
            const hoursInput = document.querySelector(`input[name="attendance[${index}][hours_worked]"]`);
            
            statusSelect.value = 'present';
            payableCheckbox.checked = true;
            hoursInput.value = hoursInput.value || '8';
            
            updateSummary();
        }

        function markAbsent(index) {
            const statusSelect = document.querySelector(`select[name="attendance[${index}][status]"]`);
            const payableCheckbox = document.querySelector(`input[name="attendance[${index}][is_payable]"]`);
            const hoursInput = document.querySelector(`input[name="attendance[${index}][hours_worked]"]`);
            
            statusSelect.value = 'absent_unauthorized';
            payableCheckbox.checked = false;
            hoursInput.value = '0';
            
            updateSummary();
        }

        function markAllPresent() {
            const statusSelects = document.querySelectorAll('select[name*="[status]"]');
            const payableCheckboxes = document.querySelectorAll('input[name*="[is_payable]"]');
            const hoursInputs = document.querySelectorAll('input[name*="[hours_worked]"]');
            
            statusSelects.forEach((select, index) => {
                select.value = 'present';
                payableCheckboxes[index].checked = true;
                hoursInputs[index].value = hoursInputs[index].value || '8';
            });
            
            updateSummary();
        }

        function updateAttendanceStatus(index) {
            const statusSelect = document.querySelector(`select[name="attendance[${index}][status]"]`);
            const payableCheckbox = document.querySelector(`input[name="attendance[${index}][is_payable]"]`);
            const hoursInput = document.querySelector(`input[name="attendance[${index}][hours_worked]"]`);
            
            // Update payable status based on attendance status
            const nonPayableStatuses = ['absent_unauthorized'];
            payableCheckbox.checked = !nonPayableStatuses.includes(statusSelect.value);
            
            // Update hours based on status
            if (statusSelect.value === 'present' || statusSelect.value === 'late') {
                hoursInput.value = hoursInput.value || '8';
            } else if (statusSelect.value === 'half_day') {
                hoursInput.value = '4';
            } else {
                hoursInput.value = '0';
            }
            
            updateSummary();
        }

        function updateSummary() {
            const statusSelects = document.querySelectorAll('select[name*="[status]"]');
            const payableCheckboxes = document.querySelectorAll('input[name*="[is_payable]"]');
            
            const counts = {
                present: 0,
                late: 0,
                'absent_unauthorized': 0,
                'absent_authorized': 0,
                excused: 0,
                'on_leave': 0,
                sick: 0,
                payable: 0
            };
            
            statusSelects.forEach(select => {
                const status = select.value;
                if (counts.hasOwnProperty(status)) {
                    counts[status]++;
                }
            });
            
            payableCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    counts.payable++;
                }
            });
            
            // Update summary display
            document.getElementById('present-count').textContent = counts.present;
            document.getElementById('late-count').textContent = counts.late;
            document.getElementById('absent-unauth-count').textContent = counts.absent_unauthorized;
            document.getElementById('absent-auth-count').textContent = counts.absent_authorized;
            document.getElementById('excused-count').textContent = counts.excused;
            document.getElementById('on-leave-count').textContent = counts.on_leave;
            document.getElementById('sick-count').textContent = counts.sick;
            document.getElementById('payable-count').textContent = counts.payable;
        }

        function saveAllAttendance() {
            const form = document.getElementById('attendance-form');
            form.submit();
        }

        // Initialize summary on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
        });
    </script>
@endsection
