<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="absent_unauthorized" {{ request('status') == 'absent_unauthorized' ? 'selected' : '' }}>Absent (Unauthorized)</option>
                                <option value="absent_authorized" {{ request('status') == 'absent_authorized' ? 'selected' : '' }}>Absent (Authorized)</option>
                                <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                                <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sick</option>
                                <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                        </div>
                        <div>
                            <label for="program_id" class="block text-sm font-medium text-gray-700">Program</label>
                            <select name="program_id" id="program_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Programs</option>
                                @foreach(\App\Models\Program::where('company_id', auth()->user()->company_id)->get() as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4">
                            <a href="{{ route('attendance.create') }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Add Attendance
                            </a>
                            <a href="{{ route('attendance.pending-proof') }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                Pending Proof ({{ \App\Models\AttendanceRecord::where('company_id', auth()->user()->company_id)->withPendingProof()->count() }})
                            </a>
                        </div>
                        <div>
                            <button onclick="openBulkMarkModal()" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Bulk Mark Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($attendanceRecords->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Learner</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In/Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proof</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($attendanceRecords as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->attendance_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->user->first_name }} {{ $record->user->last_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->program->program_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($record->status === 'present') bg-green-100 text-green-800
                                                    @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                                    @elseif($record->status === 'absent_unauthorized') bg-red-100 text-red-800
                                                    @elseif($record->status === 'absent_authorized') bg-orange-100 text-orange-800
                                                    @elseif($record->status === 'excused') bg-blue-100 text-blue-800
                                                    @elseif($record->status === 'on_leave') bg-purple-100 text-purple-800
                                                    @elseif($record->status === 'sick') bg-orange-100 text-orange-800
                                                    @elseif($record->status === 'half_day') bg-amber-100 text-amber-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucwords(str_replace('_', ' ', $record->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($record->check_in_time)
                                                    <div>In: {{ \Carbon\Carbon::parse($record->check_in_time)->format('H:i') }}</div>
                                                @endif
                                                @if($record->check_out_time)
                                                    <div>Out: {{ \Carbon\Carbon::parse($record->check_out_time)->format('H:i') }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($record->hours_worked, 2) }}h
                                                @if($record->overtime_hours > 0)
                                                    <span class="text-orange-600">(+{{ number_format($record->overtime_hours, 2) }}h OT)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                R{{ number_format($record->calculated_pay, 2) }}
                                                @if(!$record->is_payable)
                                                    <span class="text-red-500 text-xs">(Not Payable)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($record->proof_status)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($record->proof_status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($record->proof_status === 'approved') bg-green-100 text-green-800
                                                        @elseif($record->proof_status === 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($record->proof_status) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('attendance.show', $record) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">View</a>
                                                    <a href="{{ route('attendance.edit', $record) }}" 
                                                       class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                    @if($record->requiresProof() && $record->proof_status !== 'approved')
                                                        <button onclick="openProofModal({{ $record->id }})" 
                                                                class="text-blue-600 hover:text-blue-900">Upload Proof</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $attendanceRecords->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new attendance record.</p>
                            <div class="mt-6">
                                <a href="{{ route('attendance.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add Attendance
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Mark Modal -->
    <div id="bulkMarkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Mark Attendance</h3>
                <form id="bulkMarkForm">
                    @csrf
                    <div class="mb-4">
                        <label for="bulk_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="attendance_date" id="bulk_date" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="bulk_program" class="block text-sm font-medium text-gray-700">Program</label>
                        <select name="program_id" id="bulk_program" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Program</option>
                            @foreach(\App\Models\Program::where('company_id', auth()->user()->company_id)->get() as $program)
                                <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="bulk_status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="bulk_status" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Status</option>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent_unauthorized">Absent (Unauthorized)</option>
                            <option value="absent_authorized">Absent (Authorized)</option>
                            <option value="excused">Excused</option>
                            <option value="on_leave">On Leave</option>
                            <option value="sick">Sick</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="bulk_learners" class="block text-sm font-medium text-gray-700">Learners</label>
                        <select name="user_ids[]" id="bulk_learners" multiple required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->where('is_learner', true)->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="bulk_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" id="bulk_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeBulkMarkModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Mark Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Proof Upload Modal -->
    <div id="proofModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Proof Document</h3>
                <form id="proofForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="attendance_id" id="proof_attendance_id">
                    <div class="mb-4">
                        <label for="proof_document" class="block text-sm font-medium text-gray-700">Document</label>
                        <input type="file" name="proof_document" id="proof_document" required 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div class="mb-4">
                        <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                        <select name="document_type" id="document_type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Type</option>
                            <option value="medical_certificate">Medical Certificate</option>
                            <option value="emergency_document">Emergency Document</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="proof_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" id="proof_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeProofModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBulkMarkModal() {
            document.getElementById('bulkMarkModal').classList.remove('hidden');
        }

        function closeBulkMarkModal() {
            document.getElementById('bulkMarkModal').classList.add('hidden');
        }

        function openProofModal(attendanceId) {
            document.getElementById('proof_attendance_id').value = attendanceId;
            document.getElementById('proofModal').classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
        }

        // Bulk mark form submission
        document.getElementById('bulkMarkForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("attendance.bulk-mark") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    location.reload();
                } else if (data.errors) {
                    alert('Validation errors: ' + JSON.stringify(data.errors));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the request.');
            });
        });

        // Proof upload form submission
        document.getElementById('proofForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const attendanceId = document.getElementById('proof_attendance_id').value;
            
            fetch(`/attendance/${attendanceId}/upload-proof`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    location.reload();
                } else if (data.errors) {
                    alert('Validation errors: ' + JSON.stringify(data.errors));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the proof.');
            });
        });
    </script>
</x-app-layout>
