@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Schedule</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Update schedule details</p>
        </div>
        <a href="{{ route('schedules.show', $schedule) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
            Back to Schedule
        </a>
    </div>

    <form action="{{ route('schedules.update', $schedule) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="program_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program <span class="text-red-500">*</span></label>
                    <select name="program_id" id="program_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id', $schedule->program_id) == $program->id ? 'selected' : '' }}>
                                {{ $program->title }} ({{ $program->program_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructor</label>
                    <select name="instructor_id" id="instructor_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $schedule->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->first_name }} {{ $instructor->last_name }} ({{ $instructor->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $schedule->title) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $schedule->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Session Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Session Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="session_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Date <span class="text-red-500">*</span></label>
                    <input type="date" name="session_date" id="session_date" value="{{ old('session_date', $schedule->session_date) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('session_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $schedule->start_time) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $schedule->end_time) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="break_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Break Start</label>
                    <input type="time" name="break_start_time" id="break_start_time" value="{{ old('break_start_time', $schedule->break_start_time) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('break_start_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="break_end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Break End</label>
                    <input type="time" name="break_end_time" id="break_end_time" value="{{ old('break_end_time', $schedule->break_end_time) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('break_end_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="session_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Type <span class="text-red-500">*</span></label>
                    <select name="session_type" id="session_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select Type</option>
                        <option value="theory" {{ old('session_type', $schedule->session_type) == 'theory' ? 'selected' : '' }}>Theory</option>
                        <option value="practical" {{ old('session_type', $schedule->session_type) == 'practical' ? 'selected' : '' }}>Practical</option>
                        <option value="assessment" {{ old('session_type', $schedule->session_type) == 'assessment' ? 'selected' : '' }}>Assessment</option>
                        <option value="workshop" {{ old('session_type', $schedule->session_type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="field_work" {{ old('session_type', $schedule->session_type) == 'field_work' ? 'selected' : '' }}>Field Work</option>
                    </select>
                    @error('session_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Learning Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Learning Content</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="module_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module Name</label>
                    <input type="text" name="module_name" id="module_name" value="{{ old('module_name', $schedule->module_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('module_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit_standard" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Standard</label>
                    <input type="text" name="unit_standard" id="unit_standard" value="{{ old('unit_standard', $schedule->unit_standard) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('unit_standard')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="learning_outcomes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Learning Outcomes</label>
                    <textarea name="learning_outcomes" id="learning_outcomes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('learning_outcomes', $schedule->learning_outcomes) }}</textarea>
                    @error('learning_outcomes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="assessment_criteria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assessment Criteria</label>
                    <textarea name="assessment_criteria" id="assessment_criteria" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('assessment_criteria', $schedule->assessment_criteria) }}</textarea>
                    @error('assessment_criteria')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Venue & Location -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Venue & Location</h2>
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_online" id="is_online" value="1" {{ old('is_online', $schedule->is_online) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_online" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Online Session
                    </label>
                </div>
            </div>

            <!-- Physical Venue Fields -->
            <div id="physical-venue" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="venue_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Venue Name</label>
                    <input type="text" name="venue_name" id="venue_name" value="{{ old('venue_name', $schedule->venue_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('venue_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Number</label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $schedule->room_number) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('room_number')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="building" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Building</label>
                    <input type="text" name="building" id="building" value="{{ old('building', $schedule->building) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('building')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="campus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campus</label>
                    <input type="text" name="campus" id="campus" value="{{ old('campus', $schedule->campus) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('campus')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="venue_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Venue Address</label>
                    <textarea name="venue_address" id="venue_address" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('venue_address', $schedule->venue_address) }}</textarea>
                    @error('venue_address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Online Meeting Fields -->
            <div id="online-meeting" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <div>
                    <label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Platform</label>
                    <input type="text" name="platform" id="platform" value="{{ old('platform', $schedule->platform) }}" placeholder="Zoom, Teams, etc." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('platform')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meeting_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting ID</label>
                    <input type="text" name="meeting_id" id="meeting_id" value="{{ old('meeting_id', $schedule->meeting_id) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('meeting_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meeting_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting Password</label>
                    <input type="text" name="meeting_password" id="meeting_password" value="{{ old('meeting_password', $schedule->meeting_password) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('meeting_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meeting_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meeting URL</label>
                    <input type="url" name="meeting_url" id="meeting_url" value="{{ old('meeting_url', $schedule->meeting_url) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('meeting_url')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Attendance Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attendance Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="expected_attendees" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Attendees <span class="text-red-500">*</span></label>
                    <input type="number" name="expected_attendees" id="expected_attendees" min="1" value="{{ old('expected_attendees', $schedule->expected_attendees) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('expected_attendees')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="check_in_opens_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Opens</label>
                    <input type="time" name="check_in_opens_at" id="check_in_opens_at" value="{{ old('check_in_opens_at', $schedule->check_in_opens_at) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('check_in_opens_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="check_in_closes_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Closes</label>
                    <input type="time" name="check_in_closes_at" id="check_in_closes_at" value="{{ old('check_in_closes_at', $schedule->check_in_closes_at) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('check_in_closes_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center md:col-span-2">
                    <input type="checkbox" name="allow_late_check_in" id="allow_late_check_in" value="1" {{ old('allow_late_check_in', $schedule->allow_late_check_in) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="allow_late_check_in" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Allow Late Check-in
                    </label>
                </div>

                <div>
                    <label for="late_threshold_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Late Threshold (minutes)</label>
                    <input type="number" name="late_threshold_minutes" id="late_threshold_minutes" min="5" max="120" value="{{ old('late_threshold_minutes', $schedule->late_threshold_minutes) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('late_threshold_minutes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="send_reminders" id="send_reminders" value="1" {{ old('send_reminders', $schedule->send_reminders) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="send_reminders" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Send Reminders
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('schedules.show', $schedule) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Update Schedule
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isOnlineCheckbox = document.getElementById('is_online');
    const physicalVenue = document.getElementById('physical-venue');
    const onlineMeeting = document.getElementById('online-meeting');
    
    function toggleVenueFields() {
        if (isOnlineCheckbox.checked) {
            physicalVenue.classList.add('hidden');
            onlineMeeting.classList.remove('hidden');
        } else {
            physicalVenue.classList.remove('hidden');
            onlineMeeting.classList.add('hidden');
        }
    }
    
    isOnlineCheckbox.addEventListener('change', toggleVenueFields);
    toggleVenueFields(); // Initial state
});
</script>
@endsection