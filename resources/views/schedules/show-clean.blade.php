@extends('layouts.app')

@section('title', 'Schedule Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Schedule Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-8">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $schedule->title }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">{{ $schedule->program->title ?? 'No Program Associated' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex gap-2">
                        <button onclick="openModal('sendMessageModal')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                            <i class="fas fa-envelope mr-1"></i>Send Message
                        </button>
                        <a href="/schedules/{{ $schedule->id }}/attendance" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                            <i class="fas fa-clipboard-list mr-1"></i>View Attendance
                        </a>
                    </div>
                    
                    <div class="flex gap-1">
                        <a href="/schedules/{{ $schedule->id }}/export?format=pdf" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                           <i class="fas fa-file-pdf mr-1"></i>PDF
                        </a>
                        <a href="/schedules/{{ $schedule->id }}/export?format=csv" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                           <i class="fas fa-file-csv mr-1"></i>CSV
                        </a>
                        <a href="/schedules/{{ $schedule->id }}/qr-code" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-200">
                           <i class="fas fa-qrcode mr-1"></i>QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Schedule Basic Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Session Date</label>
                    <div class="text-gray-900 dark:text-white">
                        <i class="fas fa-calendar-day mr-2 text-blue-500"></i>{{ $schedule->session_date->format('M j, Y (l)') }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Session Time</label>
                    <div class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Duration</label>
                    <div class="text-gray-900 dark:text-white">{{ $schedule->planned_duration_hours ?? '0' }}h</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <div class="text-gray-900 dark:text-white">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                            @elseif($schedule->status === 'in_progress') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                            @elseif($schedule->status === 'completed') bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100
                            @elseif($schedule->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- More Schedule Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Instructor</label>
                    <div class="text-gray-900 dark:text-white">
                        {{ $schedule->instructor?->name ?? 'Not Assigned' }}
                        @if($schedule->instructor)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->instructor->email }}</div>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Program</label>
                    <div class="text-gray-900 dark:text-white">
                        {{ $schedule->program?->title ?? 'Not Assigned' }}
                        @if($schedule->program)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->program->program_code }}</div>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Venue</label>
                    <div class="text-gray-900 dark:text-white">
                        {{ $schedule->venue_name ?? 'Not Specified' }}
                        @if($schedule->venue_address)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->venue_address }}</div>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Expected Attendees</label>
                    <div class="text-gray-900 dark:text-white">{{ $schedule->expected_attendees ?? 0 }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Created By</label>
                    <div class="text-gray-900 dark:text-white">
                        {{ $schedule->creator->name ?? 'Unknown' }}
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->created_at->format('M j, Y') }}</div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Last Updated</label>
                    <div class="text-gray-900 dark:text-white">
                        {{ $schedule->updated_at->diffForHumans() }}
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule->updated_at->format('M j, Y H:i') }}</div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Check-in Window</label>
                    <div class="text-gray-900 dark:text-white">
                        @if($schedule->check_in_opens_at && $schedule->check_in_closes_at)
                            {{ \Carbon\Carbon::parse($schedule->check_in_opens_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->check_in_closes_at)->format('H:i') }}
                        @else
                            Not Set
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Check-in Status</label>
                    <div class="text-gray-900 dark:text-white">
                        @if($schedule->isCheckInOpen())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Open</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">Closed</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

            <!-- Session Summary Card -->
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between">
                    <div class="flex items-center mb-4 lg:mb-0">
                        <i class="fas fa-calendar-day text-blue-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Session Details</h3>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $schedule->session_date->format('l, F j, Y') }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} | 
                                Type: {{ ucfirst($schedule->session_type ?? 'Regular') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $attendanceSummary['present'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ ($attendanceSummary['absent_authorized'] ?? 0) + ($attendanceSummary['absent_unauthorized'] ?? 0) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $attendanceSummary['total'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                        </div>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        @if($schedule->session_date->isToday())
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">Today's Session</span>
                        @elseif($schedule->session_date->isPast())
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">Past Session</span>
                        @else
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">Upcoming Session</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Attendance Summary</h2>
                    <button onclick="openModal('bulkAttendanceModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Bulk Update
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Present Count -->
                        <div class="flex items-center">
                            <div class="bg-green-100 dark:bg-green-800 p-3 rounded-full mr-4">
                                <i class="fas fa-user-check text-2xl text-green-600 dark:text-green-200"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Present</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceSummary['present'] ?? 0 }}</div>
                            </div>
                        </div>
                        <!-- Late Count -->
                        <div class="flex items-center">
                            <div class="bg-yellow-100 dark:bg-yellow-800 p-3 rounded-full mr-4">
                                <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-200"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Late</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceSummary['late'] ?? 0 }}</div>
                            </div>
                        </div>
                        <!-- Absent Count -->
                        <div class="flex items-center">
                            <div class="bg-red-100 dark:bg-red-800 p-3 rounded-full mr-4">
                                <i class="fas fa-user-times text-2xl text-red-600 dark:text-red-200"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Absent</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ ($attendanceSummary['absent_authorized'] ?? 0) + ($attendanceSummary['absent_unauthorized'] ?? 0) }}</div>
                            </div>
                        </div>
                        <!-- Total Count -->
                        <div class="flex items-center">
                            <div class="bg-blue-100 dark:bg-blue-800 p-3 rounded-full mr-4">
                                <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-200"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Expected</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $schedule->expected_attendees ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Attendance Records</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Session attendance for {{ $schedule->session_date->format('M j, Y') }}</p>
                    </div>
                    <button onclick="openModal('addAttendanceModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Record
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Learner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($schedule->attendanceRecords as $attendance)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-200">
                                                    {{ substr($attendance->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $attendance->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($attendance->status === 'present') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                            @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                            @elseif(str_contains($attendance->status, 'absent')) bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $attendance->hours_worked ? number_format($attendance->hours_worked, 2) . 'h' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($attendance->checkInDevice)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ $attendance->checkInDevice->name ?? 'Device #' . $attendance->check_in_device_id }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Manual</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="relative inline-block text-left">
                                            <button onclick="toggleDropdown('dropdown-{{ $attendance->id }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition duration-200">
                                                Actions <i class="fas fa-chevron-down ml-1"></i>
                                            </button>
                                            <div id="dropdown-{{ $attendance->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <button onclick="editAttendance({{ $attendance->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <i class="fas fa-edit mr-2"></i>Edit
                                                    </button>
                                                    <button onclick="viewAttendanceDetails({{ $attendance->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <i class="fas fa-eye mr-2"></i>View Details
                                                    </button>
                                                    @if($attendance->has_anomaly)
                                                        <button onclick="reviewAnomalies({{ $attendance->id }})" class="block w-full text-left px-4 py-2 text-sm text-yellow-600 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>Review Anomalies
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-clipboard-list text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">No attendance records found for this session.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Bulk Attendance Modal -->
<div id="bulkAttendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Bulk Attendance Update</h3>
            <form action="/schedules/{{ $schedule->id }}/attendance/bulk-update" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Action</label>
                    <select name="bulk_action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        <option value="">Choose Action</option>
                        <option value="mark_present">Mark All Present</option>
                        <option value="mark_absent">Mark All Absent</option>
                        <option value="mark_late">Mark All Late</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" rows="3" placeholder="Bulk update notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('bulkAttendanceModal')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-save mr-1"></i> Update Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Attendance Modal -->
<div id="addAttendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add Attendance Record</h3>
            <form action="/schedules/{{ $schedule->id }}/attendance" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select User</option>
                        @foreach($schedule->program->learners ?? [] as $learner)
                            <option value="{{ $learner->id }}">{{ $learner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent_unauthorized">Absent (Unauthorized)</option>
                        <option value="absent_authorized">Absent (Authorized)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Time</label>
                        <input type="time" name="check_in_time" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-out Time</label>
                        <input type="time" name="check_out_time" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" rows="3" placeholder="Optional notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('addAttendanceModal')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-save mr-1"></i> Add Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div id="sendMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Send Message</h3>
            <form action="/schedules/{{ $schedule->id }}/send-message" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipient</label>
                    <select name="recipient_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        <option value="instructor">Instructor</option>
                        <option value="learners">All Learners</option>
                        <option value="present_learners">Present Learners Only</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                    <textarea name="message" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" rows="5" required></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('sendMessageModal')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-paper-plane mr-1"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Dropdown functions
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
        dropdowns.forEach(dropdown => {
            const button = dropdown.previousElementSibling;
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });

    // Close modals when clicking outside
    window.onclick = function(event) {
        const modals = ['bulkAttendanceModal', 'addAttendanceModal', 'sendMessageModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }

    // Attendance functions
    function editAttendance(attendanceId) {
        window.location.href = `/attendance/${attendanceId}/edit`;
    }

    function viewAttendanceDetails(attendanceId) {
        fetch(`/api/attendance/${attendanceId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Attendance details:', data);
                // You can implement a details modal here
            })
            .catch(error => {
                console.error('Error fetching attendance details:', error);
            });
    }

    function reviewAnomalies(attendanceId) {
        window.location.href = `/attendance/${attendanceId}/anomalies`;
    }
</script>

@endsection