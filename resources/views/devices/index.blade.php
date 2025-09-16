@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Device Management
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Monitor and manage registered devices
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                Total Devices
                            </p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['total'] }}
                            </h3>
                            <p class="text-xs text-info">Registered</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa-solid fa-mobile-screen text-info"></i>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                Active
                            </p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['active'] }}
                            </h3>
                            <p class="text-xs text-success">Online</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa-solid fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                Blocked
                            </p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['blocked'] }}
                            </h3>
                            <p class="text-xs text-error">Restricted</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-error/10">
                            <i class="fa-solid fa-ban text-error"></i>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">
                                Needs Approval
                            </p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $stats['needs_approval'] }}
                            </h3>
                            <p class="text-xs {{ $stats['needs_approval'] > 0 ? 'text-warning' : 'text-success' }}">
                                {{ $stats['needs_approval'] > 0 ? 'Action Required' : 'Up to Date' }}
                            </p>
                        </div>
                        <div
                            class="mask is-squircle flex size-10 items-center justify-center {{ $stats['needs_approval'] > 0 ? 'bg-warning/10' : 'bg-success/10' }}">
                            <i
                                class="fa-solid {{ $stats['needs_approval'] > 0 ? 'fa-clock text-warning' : 'fa-check text-success' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
  <!-- Filters -->
            <div class="mt-6 card">
                <div class="px-4 py-4 sm:px-5">
                    <h3 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100 mb-4">
                        Filter Devices
                    </h3>
                    <form method="GET" class="flex flex-wrap items-end gap-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-48">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Search</label>
                            <input name="search" value="{{ request('search') }}"
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                placeholder="Search devices, users, models..." />
                        </div>

                        <!-- Status Filter -->
                        <div class="min-w-32">
                            <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Status</label>
                            <select name="status"
                                class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked
                                </option>
                                <option value="needs_approval" {{ request('status') === 'needs_approval' ? 'selected' : '' }}>
                                    Needs Approval</option>
                                <option value="pending_sync" {{ request('status') === 'pending_sync' ? 'selected' : '' }}>
                                    Pending Sync</option>
                            </select>
                        </div>

                        <!-- Platform Filter -->
                        <div class="min-w-32">
                            <label
                                class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Platform</label>
                            <select name="platform"
                                class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">All Platforms</option>
                                <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                                <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android
                                </option>
                                <option value="web" {{ request('platform') === 'web' ? 'selected' : '' }}>Web</option>
                                <option value="windows" {{ request('platform') === 'windows' ? 'selected' : '' }}>Windows
                                </option>
                                <option value="macos" {{ request('platform') === 'macos' ? 'selected' : '' }}>macOS</option>
                                <option value="linux" {{ request('platform') === 'linux' ? 'selected' : '' }}>Linux</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'status', 'platform']))
                                <a href="{{ route('devices.index') }}"
                                    class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

<!-- Filters -->
<div class="card mt-6">
    <div class="p-4 sm:p-5">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-48 max-w-md">
                <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Search</label>
                <input name="search" value="{{ request('search') }}"
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Search devices, users, models..." />

            </div>

            <!-- Status Filter -->
            <div class="min-w-32 max-w-48">
                <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Status</label>
                <select name="status"
                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                    <option value="" hidden>All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    <option value="needs_approval" {{ request('status') === 'needs_approval' ? 'selected' : '' }}>Needs Approval</option>
                    <option value="pending_sync" {{ request('status') === 'pending_sync' ? 'selected' : '' }}>Pending Sync</option>
                </select>
            </div>

            <!-- Platform Filter -->
            <div class="min-w-32 max-w-52">
                <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Platform</label>
                <select name="platform"
                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                    <option value="" hidden>All Platforms</option>
                    <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                    <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android</option>
                    <option value="web" {{ request('platform') === 'web' ? 'selected' : '' }}>Web</option>
                    <option value="windows" {{ request('platform') === 'windows' ? 'selected' : '' }}>Windows</option>
                    <option value="macos" {{ request('platform') === 'macos' ? 'selected' : '' }}>macOS</option>
                    <option value="linux" {{ request('platform') === 'linux' ? 'selected' : '' }}>Linux</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit"
                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'platform']))
                    <a href="{{ route('devices.index') }}"
                        class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

            <!-- Devices Table -->
            <div class="mt-6 card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-navy-500">
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Device
                                </th>
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    User
                                </th>
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Platform
                                </th>
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Last Seen
                                </th>
                                <th
                                    class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                            @forelse($devices as $device)
                                <tr class="border-transparent hover:border-slate-200 dark:hover:border-navy-500">
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex size-8 items-center justify-center rounded-full text-xs+ font-medium text-white"
                                                style="background-color: {{ $device->getStatusColor() === 'green' ? '#10b981' : ($device->getStatusColor() === 'red' ? '#ef4444' : ($device->getStatusColor() === 'orange' ? '#f59e0b' : '#6b7280')) }}">
                                                <i class="fa-solid fa-{{ $device->getPlatformIcon() }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->device_name }}
                                                </p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $device->device_id }}
                                                </p>
                                                @if($device->device_model)
                                                    <p class="text-xs text-slate-400 dark:text-navy-300">
                                                        {{ $device->manufacturer }} {{ $device->device_model }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($device->user)
                                            <div>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $device->user->first_name }} {{ $device->user->last_name }}
                                                </p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $device->user->email }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-navy-300">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="rounded bg-slate-150 px-2 py-1 text-xs font-medium text-slate-800 dark:bg-navy-500 dark:text-navy-100">
                                                {{ ucfirst($device->platform) }}
                                            </span>
                                            @if($device->platform_version)
                                                <span class="text-xs text-slate-400 dark:text-navy-300">
                                                    v{{ $device->platform_version }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($device->is_blocked)
                                            <span class="rounded-full bg-error/10 px-2.5 py-1 text-xs+ font-medium text-error">
                                                Blocked
                                            </span>
                                        @elseif($device->isLocked())
                                            <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                                Locked
                                            </span>
                                        @elseif(!$device->is_active)
                                            <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                                Pending Approval
                                            </span>
                                        @elseif($device->is_trusted)
                                            <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                                Trusted
                                            </span>
                                        @else
                                            <span class="rounded-full bg-info/10 px-2.5 py-1 text-xs+ font-medium text-info">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        @if($device->last_seen_at)
                                            <span class="text-sm text-slate-600 dark:text-navy-200">
                                                {{ $device->last_seen_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-navy-300">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 lg:px-5">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('devices.show', $device) }}"
                                                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                <i class="fa-solid fa-eye text-slate-500 dark:text-navy-300"></i>
                                            </a>

                                            @can('manage', $device)
                                                @if(!$device->is_active && !$device->is_blocked)
                                                    <form method="POST" action="{{ route('devices.approve', $device) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="btn size-8 rounded-full p-0 hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                            title="Approve Device">
                                                            <i class="fa-solid fa-check text-success"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(!$device->is_blocked)
                                                    <button onclick="blockDevice({{ $device->id }})"
                                                        class="btn size-8 rounded-full p-0 hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                        title="Block Device">
                                                        <i class="fa-solid fa-ban text-error"></i>
                                                    </button>
                                                @else
                                                    <form method="POST" action="{{ route('devices.unblock', $device) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="btn size-8 rounded-full p-0 hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                            title="Unblock Device">
                                                            <i class="fa-solid fa-unlock text-success"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <i
                                                class="fa-solid fa-mobile-screen-button text-4xl text-slate-300 dark:text-navy-400"></i>
                                            <p class="text-slate-600 dark:text-navy-200">No devices found</p>
                                            @if(request()->hasAny(['search', 'status', 'platform']))
                                                <a href="{{ route('devices.index') }}"
                                                    class="text-primary hover:text-primary-focus">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($devices->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                        {{ $devices->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Block Device Modal -->
    <div id="blockDeviceModal"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 opacity-0 sm:px-5"
        x-show="false" style="display: none;">
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
            
            <form id="blockDeviceForm" method="POST">
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

    <script>
        let blockModal;
        let blockForm;

        document.addEventListener('DOMContentLoaded', function () {
            blockModal = document.getElementById('blockDeviceModal');
            blockForm = document.getElementById('blockDeviceForm');
        });

        function blockDevice(deviceId) {
            blockForm.action = `/devices/${deviceId}/block`;
            blockModal.style.display = 'flex';
            blockModal.classList.remove('opacity-0');
            blockModal.classList.add('opacity-100');
        }

        function closeBlockModal() {
            blockModal.classList.remove('opacity-100');
            blockModal.classList.add('opacity-0');
            setTimeout(() => {
                blockModal.style.display = 'none';
            }, 300);
        }
    </script>
@endsection