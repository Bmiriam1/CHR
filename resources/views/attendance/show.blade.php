<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Record Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $attendance->user->first_name }} {{ $attendance->user->last_name }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $attendance->attendance_date->format('F d, Y') }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('attendance.edit', $attendance) }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                Edit
                            </a>
                            <a href="{{ route('attendance.index') }}" 
                               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h4>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Learner</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $attendance->user->first_name }} {{ $attendance->user->last_name }}
                                    <span class="text-gray-500">({{ $attendance->user->employee_number }})</span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Program</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $attendance->program->program_name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $attendance->attendance_date->format('F d, Y') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($attendance->status === 'present') bg-green-100 text-green-800
                                        @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                        @elseif($attendance->status === 'absent_unauthorized') bg-red-100 text-red-800
                                        @elseif($attendance->status === 'absent_authorized') bg-orange-100 text-orange-800
                                        @elseif($attendance->status === 'excused') bg-blue-100 text-blue-800
                                        @elseif($attendance->status === 'on_leave') bg-purple-100 text-purple-800
                                        @elseif($attendance->status === 'sick') bg-orange-100 text-orange-800
                                        @elseif($attendance->status === 'half_day') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $attendance->status)) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Payable</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $attendance->is_payable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $attendance->is_payable ? 'Yes' : 'No' }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Calculated Pay</dt>
                                <dd class="mt-1 text-sm text-gray-900">R{{ number_format($attendance->calculated_pay, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Time Tracking -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Time Tracking</h4>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check In Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : 'Not recorded' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check Out Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : 'Not recorded' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hours Worked</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($attendance->hours_worked, 2) }} hours</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Overtime Hours</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($attendance->overtime_hours, 2) }} hours</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Break Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($attendance->break_duration, 2) }} hours</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Partial Day</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $attendance->is_partial_day ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $attendance->is_partial_day ? 'Yes (' . number_format($attendance->partial_day_percentage, 1) . '%)' : 'No' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- QR Code & Location -->
                @if($attendance->check_in_qr_code || $attendance->check_out_qr_code)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">QR Code & Location</h4>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            @if($attendance->check_in_qr_code)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check In QR Code</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $attendance->check_in_qr_code }}</dd>
                            </div>
                            @endif
                            
                            @if($attendance->check_out_qr_code)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check Out QR Code</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $attendance->check_out_qr_code }}</dd>
                            </div>
                            @endif
                            
                            @if($attendance->check_in_location_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check In Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $attendance->check_in_location_name }}</dd>
                            </div>
                            @endif
                            
                            @if($attendance->check_out_location_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check Out Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $attendance->check_out_location_name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                @endif

                <!-- Proof Document -->
                @if($attendance->proof_document_path)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Proof Document</h4>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Document Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $attendance->proof_document_type)) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($attendance->proof_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($attendance->proof_status === 'approved') bg-green-100 text-green-800
                                        @elseif($attendance->proof_status === 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($attendance->proof_status) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Uploaded At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $attendance->proof_uploaded_at?->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Actions</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('attendance.download-proof', $attendance) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm">Download Document</a>
                                </dd>
                            </div>
                        </dl>
                        
                        @if($attendance->proof_notes)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $attendance->proof_notes }}</dd>
                        </div>
                        @endif
                        
                        @if($attendance->proof_approval_notes)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Approval Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $attendance->proof_approval_notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Notes & Comments -->
                @if($attendance->notes || $attendance->exception_reason)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Notes & Comments</h4>
                        
                        @if($attendance->exception_reason)
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Exception Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $attendance->exception_reason }}</dd>
                        </div>
                        @endif
                        
                        @if($attendance->notes)
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $attendance->notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Proof Management Actions -->
            @if($attendance->requiresProof() && $attendance->proof_status !== 'approved')
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Proof Management</h4>
                    
                    @if($attendance->proof_status === 'pending')
                    <div class="flex space-x-4">
                        <button onclick="approveProof({{ $attendance->id }})" 
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Approve Proof
                        </button>
                        <button onclick="rejectProof({{ $attendance->id }})" 
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reject Proof
                        </button>
                    </div>
                    @elseif(!$attendance->proof_document_path)
                    <div>
                        <button onclick="openProofUploadModal({{ $attendance->id }})" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Upload Proof Document
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Proof Upload Modal -->
    <div id="proofUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Proof Document</h3>
                <form id="proofUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="attendance_id" id="proof_upload_attendance_id">
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
                        <button type="button" onclick="closeProofUploadModal()" 
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
        function openProofUploadModal(attendanceId) {
            document.getElementById('proof_upload_attendance_id').value = attendanceId;
            document.getElementById('proofUploadModal').classList.remove('hidden');
        }

        function closeProofUploadModal() {
            document.getElementById('proofUploadModal').classList.add('hidden');
        }

        function approveProof(attendanceId) {
            if (confirm('Are you sure you want to approve this proof document?')) {
                fetch(`/attendance/${attendanceId}/approve-proof`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the proof.');
                });
            }
        }

        function rejectProof(attendanceId) {
            const notes = prompt('Please provide a reason for rejection:');
            if (notes) {
                fetch(`/attendance/${attendanceId}/reject-proof`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ notes: notes })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the proof.');
                });
            }
        }

        // Proof upload form submission
        document.getElementById('proofUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const attendanceId = document.getElementById('proof_upload_attendance_id').value;
            
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
