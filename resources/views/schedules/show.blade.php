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
                            {{ $schedule->updated_at->diffForHumans() }}
                            <small class="text-muted d-block">{{ $schedule->updated_at->format('M j, Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">Check-in Window</label>
                        <div class="fs-6 text-dark">
                            @if($schedule->check_in_opens_at && $schedule->check_in_closes_at)
                                {{ \Carbon\Carbon::parse($schedule->check_in_opens_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->check_in_closes_at)->format('H:i') }}
                            @else
                                Not Set
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">Check-in Status</label>
                        <div class="fs-6 text-dark">
                            @if($schedule->isCheckInOpen())
                                <span class="badge badge-success">Open</span>
                            @else
                                <span class="badge badge-secondary">Closed</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="separator my-5"></div>

                <!-- Current Date Quick Summary -->
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="card bg-light-primary">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-day text-primary fs-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Session Details</h6>
                                            <h5 class="mb-0 text-dark">{{ $schedule->session_date->format('l, F j, Y') }}</h5>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} | 
                                                Type: {{ ucfirst($schedule->session_type ?? 'Regular') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="d-flex gap-3">
                                            <div class="text-center">
                                                <div class="fs-2 fw-bold text-success">{{ $attendanceSummary['present'] ?? 0 }}</div>
                                                <small class="text-muted">Present</small>
                                            </div>
                                            <div class="text-center">
                                                <div class="fs-2 fw-bold text-warning">{{ ($attendanceSummary['absent_authorized'] ?? 0) + ($attendanceSummary['absent_unauthorized'] ?? 0) }}</div>
                                                <small class="text-muted">Absent</small>
                                            </div>
                                            <div class="text-center">
                                                <div class="fs-2 fw-bold text-primary">{{ $attendanceSummary['total'] ?? 0 }}</div>
                                                <small class="text-muted">Total</small>
                                            </div>
                                        </div>
                                        @if($schedule->session_date->isToday())
                                            <span class="badge badge-warning mt-2">Today's Session</span>
                                        @elseif($schedule->session_date->isPast())
                                            <span class="badge badge-secondary mt-2">Past Session</span>
                                        @else
                                            <span class="badge badge-info mt-2">Upcoming Session</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary Card -->
                <div class="card mb-5">
                    <div class="card-header">
                        <div class="card-title">
                            <h4 class="fw-bold m-0">Attendance Summary</h4>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex gap-2 align-items-center">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal">
                                    <i class="fas fa-edit me-2"></i>Bulk Update
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Present Count -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-success">
                                            <i class="fas fa-user-check fs-2 text-success"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Present</span>
                                        <h2 class="fw-bold present-count">{{ $attendanceSummary['present'] ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                            <!-- Late Count -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-warning">
                                            <i class="fas fa-clock fs-2 text-warning"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Late</span>
                                        <h2 class="fw-bold late-count">{{ $attendanceSummary['late'] ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                            <!-- Absent Count -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-danger">
                                            <i class="fas fa-user-times fs-2 text-danger"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Absent</span>
                                        <h2 class="fw-bold absent-count">{{ ($attendanceSummary['absent_authorized'] ?? 0) + ($attendanceSummary['absent_unauthorized'] ?? 0) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <!-- Total Count -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="fas fa-users fs-2 text-primary"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Total Expected</span>
                                        <h2 class="fw-bold total-count">{{ $schedule->expected_attendees ?? 0 }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records Table -->
                <div class="mb-10">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold m-0">Attendance Records</h4>
                            <small class="text-muted">Session attendance for {{ $schedule->session_date->format('M j, Y') }}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                                <i class="fas fa-plus me-1"></i> Add Record
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-dashed gy-4 align-middle">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800">
                                    <th>Learner</th>
                                    <th>Status</th>
                                    <th>Check-in Time</th>
                                    <th>Check-out Time</th>
                                    <th>Hours Worked</th>
                                    <th>Device Used</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedule->attendanceRecords as $attendance)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-3">
                                                    <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                                        {{ substr($attendance->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('users.show', $attendance->user) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ $attendance->user->name }}
                                                    </a>
                                                    <div class="text-muted fs-7">{{ $attendance->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $attendance->getStatusColor() }}">
                                                {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                        </td>
                                        <td>
                                            {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                        </td>
                                        <td>
                                            {{ $attendance->hours_worked ? number_format($attendance->hours_worked, 2) . 'h' : '-' }}
                                        </td>
                                        <td>
                                            @if($attendance->checkInDevice)
                                                <span class="badge badge-light">{{ $attendance->checkInDevice->name ?? 'Device #' . $attendance->check_in_device_id }}</span>
                                            @else
                                                <span class="text-muted">Manual</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light-primary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" onclick="editAttendance({{ $attendance->id }})">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="viewAttendanceDetails({{ $attendance->id }})">
                                                            <i class="fas fa-eye me-2"></i>View Details
                                                        </button>
                                                    </li>
                                                    @if($attendance->has_anomaly)
                                                        <li>
                                                            <button class="dropdown-item text-warning" onclick="reviewAnomalies({{ $attendance->id }})">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>Review Anomalies
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-clipboard-list fs-2x mb-3"></i>
                                            <p class="mb-0">No attendance records found for this session.</p>
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

    <!-- Bulk Attendance Modal -->
    <div class="modal fade" id="bulkAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Attendance Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('schedules.attendance.bulk-update', $schedule) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Action</label>
                            <select name="bulk_action" class="form-select" required>
                                <option value="">Choose Action</option>
                                <option value="mark_present">Mark All Present</option>
                                <option value="mark_absent">Mark All Absent</option>
                                <option value="mark_late">Mark All Late</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Bulk update notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Attendance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('schedules.attendance.store', $schedule) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select User</option>
                                @foreach($schedule->program->learners ?? [] as $learner)
                                    <option value="{{ $learner->id }}">{{ $learner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent_unauthorized">Absent (Unauthorized)</option>
                                <option value="absent_authorized">Absent (Authorized)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Check-in Time</label>
                                    <input type="time" name="check_in_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Check-out Time</label>
                                    <input type="time" name="check_out_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Send Message Modal -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('schedules.send-message', $schedule) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Recipient</label>
                            <select name="recipient_type" class="form-select" required>
                                <option value="instructor">Instructor</option>
                                <option value="learners">All Learners</option>
                                <option value="present_learners">Present Learners Only</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editAttendance(attendanceId) {
        // Implementation for editing attendance
        window.location.href = `/attendance/${attendanceId}/edit`;
    }

    function viewAttendanceDetails(attendanceId) {
        // Implementation for viewing attendance details
        fetch(`/api/attendance/${attendanceId}`)
            .then(response => response.json())
            .then(data => {
                // Show details in a modal or redirect
                console.log('Attendance details:', data);
            });
    }

    function reviewAnomalies(attendanceId) {
        // Implementation for reviewing anomalies
        window.location.href = `/attendance/${attendanceId}/anomalies`;
    }
</script>
@endsection
                    <div class="flex items-center mt-1">
                        @php
                            $statusClasses = [
                                'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$schedule->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Attendance</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        {{ $schedule->actual_attendees ?? 0 }} / {{ $schedule->expected_attendees }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Session Type -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Session Type</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        {{ ucfirst(str_replace('_', ' ', $schedule->session_type)) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Duration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_time);
                            $end = \Carbon\Carbon::parse($schedule->end_time);
                            $duration = $end->diff($start);
                        @endphp
                        {{ $duration->format('%h:%I') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Session Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Session Information</h2>
                <div class="space-y-4">
                    @if($schedule->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->description }}</dd>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($schedule->session_date)->format('M j, Y') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                            </dd>
                        </div>

                        @if($schedule->break_start_time && $schedule->break_end_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Break Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($schedule->break_start_time)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->break_end_time)->format('g:i A') }}
                                </dd>
                            </div>
                        @endif

                        @if($schedule->instructor)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Instructor</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $schedule->instructor->first_name }} {{ $schedule->instructor->last_name }}
                                    <span class="text-gray-500">({{ $schedule->instructor->email }})</span>
                                </dd>
                            </div>
                        @endif
                    </div>
                    
                    @if($schedule->creator)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $schedule->creator->first_name }} {{ $schedule->creator->last_name }}
                                <span class="text-gray-500">on {{ $schedule->created_at->format('M j, Y') }}</span>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Learning Content -->
            @if($schedule->module_name || $schedule->unit_standard || $schedule->learning_outcomes || $schedule->assessment_criteria)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Learning Content</h2>
                    <div class="space-y-4">
                        @if($schedule->module_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Module Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->module_name }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->unit_standard)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Standard</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->unit_standard }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->learning_outcomes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Learning Outcomes</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $schedule->learning_outcomes }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->assessment_criteria)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assessment Criteria</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $schedule->assessment_criteria }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Attendance Records -->
            @if($schedule->attendanceRecords && $schedule->attendanceRecords->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attendance Records</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Learner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($schedule->attendanceRecords as $record)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $record->user->first_name }} {{ $record->user->last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('g:i A') : 'Not checked in' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('g:i A') : 'Not checked out' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $attendanceStatusClasses = [
                                                    'present' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                    'late' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                    'absent' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                                    'excused' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $attendanceStatusClasses[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('update', $schedule)
                        <a href="{{ route('schedules.edit', $schedule) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 block text-center">
                            Edit Schedule
                        </a>
                    @endcan
                    
                    @can('manage', $schedule)
                        @if($schedule->status === 'scheduled')
                            <form action="{{ route('schedules.start', $schedule) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    Start Session
                                </button>
                            </form>
                        @elseif($schedule->status === 'in_progress')
                            <form action="{{ route('schedules.complete', $schedule) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    Complete Session
                                </button>
                            </form>
                        @endif

                        @if($schedule->status !== 'completed' && $schedule->status !== 'cancelled')
                            <button onclick="showCancelModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Cancel Session
                            </button>
                        @endif
                    @endcan
                    
                    @if($schedule->qr_code_active && $schedule->status !== 'cancelled')
                        <a href="{{ route('schedules.qr-code', $schedule) }}" target="_blank" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200 block text-center">
                            View QR Code
                        </a>
                    @endif
                </div>
            </div>

            <!-- Location & Venue -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location & Venue</h3>
                <div class="space-y-3">
                    @if($schedule->is_online)
                        <div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Online Session</span>
                            </div>
                        </div>
                        
                        @if($schedule->platform)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->platform }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->meeting_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meeting ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $schedule->meeting_id }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->meeting_password)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Password</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $schedule->meeting_password }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->meeting_url)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meeting URL</dt>
                                <dd class="mt-1">
                                    <a href="{{ $schedule->meeting_url }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        Join Meeting
                                    </a>
                                </dd>
                            </div>
                        @endif
                    @else
                        @if($schedule->venue_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->venue_name }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->room_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Room</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->room_number }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->building)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Building</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->building }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->campus)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Campus</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->campus }}</dd>
                            </div>
                        @endif
                        
                        @if($schedule->venue_address)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->venue_address }}</dd>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Program Info -->
            @if($schedule->program)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Program Information</h3>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->program->title }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->program->program_code }}</dd>
                        </div>
                        
                        <div>
                            <a href="{{ route('programs.show', $schedule->program) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                View Program Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attendance Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attendance Settings</h3>
                <div class="space-y-3">
                    @if($schedule->check_in_opens_at && $schedule->check_in_closes_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Check-in Window</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($schedule->check_in_opens_at)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($schedule->check_in_closes_at)->format('g:i A') }}
                            </dd>
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Late Check-in</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $schedule->allow_late_check_in ? 'Allowed' : 'Not allowed' }}
                        </dd>
                    </div>
                    
                    @if($schedule->allow_late_check_in && $schedule->late_threshold_minutes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Late Threshold</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schedule->late_threshold_minutes }} minutes</dd>
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Send Reminders</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $schedule->send_reminders ? 'Yes' : 'No' }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Session Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cancel Session</h3>
            <form action="{{ route('schedules.cancel', $schedule) }}" method="POST" class="mt-4">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for cancellation</label>
                    <textarea name="reason" id="reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="hideCancelModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Cancel Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('cancelModal');
    if (event.target === modal) {
        hideCancelModal();
    }
}
</script>
@endsection