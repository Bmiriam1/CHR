<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Attendance Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Learner Selection -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Learner *</label>
                                <select name="user_id" id="user_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('user_id') border-red-300 @enderror">
                                    <option value="">Select Learner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->employee_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Program Selection -->
                            <div>
                                <label for="program_id" class="block text-sm font-medium text-gray-700">Program
                                    *</label>
                                <select name="program_id" id="program_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('program_id') border-red-300 @enderror">
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Attendance Date -->
                            <div>
                                <label for="attendance_date" class="block text-sm font-medium text-gray-700">Date
                                    *</label>
                                <input type="date" name="attendance_date" id="attendance_date"
                                    value="{{ old('attendance_date', now()->toDateString()) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('attendance_date') border-red-300 @enderror">
                                @error('attendance_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-300 @enderror">
                                    <option value="">Select Status</option>
                                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present
                                    </option>
                                    <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="absent_unauthorized" {{ old('status') == 'absent_unauthorized' ? 'selected' : '' }}>Absent (Unauthorized)</option>
                                    <option value="absent_authorized" {{ old('status') == 'absent_authorized' ? 'selected' : '' }}>Absent (Authorized)</option>
                                    <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Excused
                                    </option>
                                    <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>On Leave
                                    </option>
                                    <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sick</option>
                                    <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half Day
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check In Time -->
                            <div>
                                <label for="check_in_time" class="block text-sm font-medium text-gray-700">Check In
                                    Time</label>
                                <input type="time" name="check_in_time" id="check_in_time"
                                    value="{{ old('check_in_time') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('check_in_time') border-red-300 @enderror">
                                @error('check_in_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check Out Time -->
                            <div>
                                <label for="check_out_time" class="block text-sm font-medium text-gray-700">Check Out
                                    Time</label>
                                <input type="time" name="check_out_time" id="check_out_time"
                                    value="{{ old('check_out_time') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('check_out_time') border-red-300 @enderror">
                                @error('check_out_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Exception Reason -->
                        <div class="mt-6">
                            <label for="exception_reason" class="block text-sm font-medium text-gray-700">Exception
                                Reason</label>
                            <textarea name="exception_reason" id="exception_reason" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('exception_reason') border-red-300 @enderror"
                                placeholder="Reason for absence or exception">{{ old('exception_reason') }}</textarea>
                            @error('exception_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-300 @enderror"
                                placeholder="Additional notes or comments">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('attendance.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Create Attendance Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
