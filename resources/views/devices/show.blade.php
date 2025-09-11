@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1">
                    <a href="{{ route('devices.index') }}"
                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                        <i class="fa-solid fa-chevron-left text-base"></i>
                    </a>
                    <div>
                        <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                            Device Details
                        </h2>
                        <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                            {{ $device->device_name }} - {{ $device->device_id }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @can('update', $device)
                        <a href="{{ route('devices.edit', $device) }}"
                            class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                            <i class="fa-solid fa-edit mr-2"></i>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Device Information -->
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-4">
                                <div class="flex size-16 items-center justify-center rounded-full text-2xl text-white"
                                    style="background-color: {{ $device->getStatusColor() === 'green' ? '#10b981' : ($device->getStatusColor() === 'red' ? '#ef4444' : ($device->getStatusColor() === 'orange' ? '#f59e0b' : '#6b7280')) }}">
                                    <i class="fa-solid fa-{{ $device->getPlatformIcon() }}"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                        {{ $device->device_name }}
                                    </h3>
                                    <p class="text-slate-400 dark:text-navy-300">{{ $device->device_id }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        @if($device->is_blocked)
                                            <span class="rounded-full bg-error/10 px-2.5 py-1 text-xs+ font-medium text-error">
                                                Blocked
                                            </span>
                                        @elseif($device->isLocked())
                                            <span
                                                class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                                Locked
                                            </span>
                                        @elseif(!$device->is_active)
                                            <span
                                                class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                                Pending Approval
                                            </span>
                                        @elseif($device->is_trusted)
                                            <span
                                                class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                                Trusted
                                            </span>
                                        @else
                                            <span class="rounded-full bg-info/10 px-2.5 py-1 text-xs+ font-medium text-info">
                                                Active
                                            </span>
                                        @endif

                                        @if($device->push_notifications_enabled)
                                            <span
                                                class="rounded-full bg-primary/10 px-2.5 py-1 text-xs+ font-medium text-primary">
                                                Push Enabled
                                            </span>
                                        @endif

                                        @if($device->biometric_enabled)
                                            <span
                                                class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                                Biometric
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Device Info -->
                                <div class="space-y-4">
                                    <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">Device Information
                                    </h4>

                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600 dark:text-navy-200">Platform</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ ucfirst($device->platform) }} {{ $device->platform_version }}
                                            </span>
                                        </div>

                                        @if($device->device_model)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600 dark:text-navy-200">Model</span>
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->manufacturer }} {{ $device->device_model }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($device->app_version)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600 dark:text-navy-200">App Version</span>
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->app_version }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($device->browser)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600 dark:text-navy-200">Browser</span>
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->browser }} {{ $device->browser_version }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Activity Info -->
                                <div class="space-y-4">
                                    <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">Activity</h4>

                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600 dark:text-navy-200">First Seen</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $device->first_seen_at ? $device->first_seen_at->format('M d, Y H:i') : 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-slate-600 dark:text-navy-200">Last Seen</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}
                                            </span>
                                        </div>

                                        @if($device->last_login_at)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600 dark:text-navy-200">Last Login</span>
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->last_login_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($device->ip_address)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600 dark:text-navy-200">IP Address</span>
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->ip_address }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Capabilities -->
                            <div class="mt-6 border-t border-slate-200 pt-6 dark:border-navy-500">
                                <h4 class="mb-4 text-base font-medium text-slate-700 dark:text-navy-100">Capabilities</h4>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="fa-solid fa-qrcode text-{{ $device->supports_qr_scanning ? 'success' : 'slate-400' }}"></i>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">QR Scanning</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="fa-solid fa-location-dot text-{{ $device->supports_gps ? 'success' : 'slate-400' }}"></i>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">GPS</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="fa-solid fa-camera text-{{ $device->supports_camera ? 'success' : 'slate-400' }}"></i>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Camera</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i
                                            class="fa-solid fa-wifi text-{{ $device->supports_offline_mode ? 'success' : 'slate-400' }}"></i>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">Offline Mode</span>
                                    </div>
                                </div>
                            </div>

                            @if($device->registration_notes)
                                <div class="mt-6 border-t border-slate-200 pt-6 dark:border-navy-500">
                                    <h4 class="mb-2 text-base font-medium text-slate-700 dark:text-navy-100">Registration Notes
                                    </h4>
                                    <p class="text-slate-600 dark:text-navy-200">{{ $device->registration_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- User Info -->
                    @if($device->user)
                        <div class="card">
                            <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">User</h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                            {{ $device->user->first_name }} {{ $device->user->last_name }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">{{ $device->user->email }}</p>
                                        @if($device->user->employee_number)
                                            <p class="text-xs text-slate-400 dark:text-navy-300">
                                                {{ $device->user->employee_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    @can('manage', $device)
                        <div class="card">
                            <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Quick Actions</h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="space-y-3">
                                    @if(!$device->is_active && !$device->is_blocked)
                                        <form method="POST" action="{{ route('devices.approve', $device) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                                <i class="fa-solid fa-check mr-2"></i>
                                                Approve Device
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$device->is_blocked)
                                        <button onclick="blockDevice()"
                                            class="btn w-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                                            <i class="fa-solid fa-ban mr-2"></i>
                                            Block Device
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('devices.unblock', $device) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn w-full bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                                <i class="fa-solid fa-unlock mr-2"></i>
                                                Unblock Device
                                            </button>
                                        </form>
                                    @endif

                                    @can('delete', $device)
                                        <button onclick="confirmDelete()"
                                            class="btn w-full border border-error font-medium text-error hover:bg-error hover:text-white focus:bg-error focus:text-white">
                                            <i class="fa-solid fa-trash mr-2"></i>
                                            Delete Device
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- Status History -->
                    @if($device->approved_at || $device->blocked_at)
                        <div class="card">
                            <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Status History</h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="space-y-4">
                                    @if($device->approved_at)
                                        <div class="flex items-start space-x-3">
                                            <div
                                                class="mt-1 flex size-6 items-center justify-center rounded-full bg-success/10 text-success">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-700 dark:text-navy-100">Device Approved</p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $device->approved_at->format('M d, Y H:i') }}
                                                    @if($device->approver)
                                                        by {{ $device->approver->first_name }} {{ $device->approver->last_name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($device->blocked_at)
                                        <div class="flex items-start space-x-3">
                                            <div
                                                class="mt-1 flex size-6 items-center justify-center rounded-full bg-error/10 text-error">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-700 dark:text-navy-100">Device Blocked</p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $device->blocked_at->format('M d, Y H:i') }}
                                                    @if($device->blocker)
                                                        by {{ $device->blocker->first_name }} {{ $device->blocker->last_name }}
                                                    @endif
                                                </p>
                                                @if($device->block_reason)
                                                    <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">
                                                        Reason: {{ $device->block_reason }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Attendance -->
            @if($recentAttendance->count() > 0)
                <div class="mt-6">
                    <div class="card">
                        <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Recent Attendance (Last 10)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-navy-500">
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Date
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Program
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Check In
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Check Out
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Duration
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                    @foreach($recentAttendance as $attendance)
                                        <tr>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $attendance->check_in_time ? $attendance->check_in_time->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                @if($attendance->program)
                                                    <span class="text-slate-600 dark:text-navy-200">
                                                        {{ $attendance->program->name }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-navy-300">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                @if($attendance->check_in_time)
                                                    <span class="text-slate-600 dark:text-navy-200">
                                                        {{ $attendance->check_in_time->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-navy-300">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                @if($attendance->check_out_time)
                                                    <span class="text-slate-600 dark:text-navy-200">
                                                        {{ $attendance->check_out_time->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-navy-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                @if($attendance->check_in_time && $attendance->check_out_time)
                                                    <span class="text-slate-600 dark:text-navy-200">
                                                        {{ $attendance->check_in_time->diffInHours($attendance->check_out_time) }}h
                                                        {{ $attendance->check_in_time->diffInMinutes($attendance->check_out_time) % 60 }}m
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-navy-300">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Block Device Modal -->
    <div id="blockDeviceModal"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 opacity-0 sm:px-5"
        style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>
        <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700">
            <div class="flex items-center justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                    Block Device
                </h3>
                <button onclick="closeBlockModal()"
                    class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('devices.block', $device) }}">
                @csrf
                @method('PATCH')
                <div class="px-4 py-4 sm:px-5">
                    <p class="mb-4 text-slate-600 dark:text-navy-200">
                        Are you sure you want to block this device? Please provide a reason:
                    </p>
                    <label class="block">
                        <span class="text-xs+ font-medium text-slate-700 dark:text-navy-100">Reason</span>
                        <textarea name="reason"
                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                            rows="3" placeholder="Enter reason for blocking this device..." required></textarea>
                    </label>
                </div>
                <div class="flex justify-end space-x-2 px-4 py-3 sm:px-5">
                    <button type="button" onclick="closeBlockModal()"
                        class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="btn min-w-[7rem] bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                        Block Device
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 opacity-0 sm:px-5"
        style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>
        <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700">
            <div class="flex items-center justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                    Delete Device
                </h3>
                <button onclick="closeDeleteModal()"
                    class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="px-4 py-4 sm:px-5">
                <p class="text-slate-600 dark:text-navy-200">
                    Are you sure you want to delete this device? This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-end space-x-2 px-4 py-3 sm:px-5">
                <button type="button" onclick="closeDeleteModal()"
                    class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                    Cancel
                </button>
                <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="btn min-w-[7rem] bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                        Delete Device
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function blockDevice() {
            const modal = document.getElementById('blockDeviceModal');
            modal.style.display = 'flex';
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
        }

        function closeBlockModal() {
            const modal = document.getElementById('blockDeviceModal');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    </script>
@endsection