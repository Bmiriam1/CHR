@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page header -->
    <div class="flex items-center justify-between py-5 lg:py-6">
        <div class="flex items-center space-x-1">
            <a href="{{ route('devices.show', $device) }}" 
               class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                <i class="fa-solid fa-chevron-left text-base"></i>
            </a>
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Edit Device
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('devices.update', $device) }}">
                @csrf
                @method('PATCH')
                
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Device Settings</h3>
                    </div>
                    
                    <div class="space-y-6 p-4 sm:p-5">
                        <!-- Device Name -->
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Device Name</span>
                            <span class="text-error">*</span>
                            <input name="device_name" 
                                   value="{{ old('device_name', $device->device_name) }}"
                                   class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('device_name') border-error @enderror"
                                   placeholder="Enter device name" 
                                   required />
                            @error('device_name')
                                <span class="text-xs text-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <!-- Security Settings -->
                        <div class="space-y-4">
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">Security Settings</h4>
                            
                            <label class="inline-flex items-center space-x-2">
                                <input name="is_trusted" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('is_trusted', $device->is_trusted) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-5 rounded border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-700 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent" />
                                <div>
                                    <span class="text-slate-700 dark:text-navy-100">Trusted Device</span>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Mark this device as trusted for enhanced access</p>
                                </div>
                            </label>

                            <label class="inline-flex items-center space-x-2">
                                <input name="biometric_enabled" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('biometric_enabled', $device->biometric_enabled) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-5 rounded border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-700 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent" />
                                <div>
                                    <span class="text-slate-700 dark:text-navy-100">Biometric Authentication</span>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Enable biometric authentication for this device</p>
                                </div>
                            </label>
                        </div>

                        <!-- Attendance Settings -->
                        <div class="space-y-4">
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">Attendance Settings</h4>
                            
                            <label class="inline-flex items-center space-x-2">
                                <input name="require_location_for_checkin" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('require_location_for_checkin', $device->require_location_for_checkin) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-5 rounded border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-700 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent" />
                                <div>
                                    <span class="text-slate-700 dark:text-navy-100">Require Location for Check-in</span>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Require GPS location to be enabled during check-in</p>
                                </div>
                            </label>

                            <label class="inline-flex items-center space-x-2">
                                <input name="auto_checkout_enabled" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('auto_checkout_enabled', $device->auto_checkout_enabled) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-5 rounded border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-700 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent"
                                       onchange="toggleAutoCheckoutHours(this)" />
                                <div>
                                    <span class="text-slate-700 dark:text-navy-100">Auto Check-out</span>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Automatically check out after specified hours</p>
                                </div>
                            </label>

                            <div id="auto_checkout_hours_container" 
                                 class="ml-7 {{ old('auto_checkout_enabled', $device->auto_checkout_enabled) ? '' : 'hidden' }}">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Auto Check-out Hours</span>
                                    <input name="auto_checkout_hours" 
                                           type="number" 
                                           min="1" 
                                           max="24" 
                                           value="{{ old('auto_checkout_hours', $device->auto_checkout_hours ?? 8) }}"
                                           class="form-input mt-1.5 w-full max-w-32 rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="8" />
                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Hours after which to auto check-out</p>
                                </label>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="space-y-4">
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">Notification Settings</h4>
                            
                            <label class="inline-flex items-center space-x-2">
                                <input name="push_notifications_enabled" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('push_notifications_enabled', $device->push_notifications_enabled) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-5 rounded border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-700 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent" />
                                <div>
                                    <span class="text-slate-700 dark:text-navy-100">Push Notifications</span>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Enable push notifications for this device</p>
                                </div>
                            </label>
                        </div>

                        <!-- Registration Notes -->
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Registration Notes</span>
                            <textarea name="registration_notes" 
                                      rows="3"
                                      class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('registration_notes') border-error @enderror"
                                      placeholder="Add any notes about this device registration...">{{ old('registration_notes', $device->registration_notes) }}</textarea>
                            @error('registration_notes')
                                <span class="text-xs text-error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="border-t border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('devices.show', $device) }}" 
                               class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Device Info -->
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Device Information</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="flex size-12 items-center justify-center rounded-full text-lg text-white" 
                             style="background-color: {{ $device->getStatusColor() === 'green' ? '#10b981' : ($device->getStatusColor() === 'red' ? '#ef4444' : ($device->getStatusColor() === 'orange' ? '#f59e0b' : '#6b7280')) }}">
                            <i class="fa-solid fa-{{ $device->getPlatformIcon() }}"></i>
                        </div>
                        <div>
                            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $device->device_name }}</p>
                            <p class="text-xs text-slate-400 dark:text-navy-300">{{ $device->device_id }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-navy-200">Platform:</span>
                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                {{ ucfirst($device->platform) }}
                            </span>
                        </div>
                        
                        @if($device->device_model)
                            <div class="flex justify-between">
                                <span class="text-slate-600 dark:text-navy-200">Model:</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ $device->manufacturer }} {{ $device->device_model }}
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-navy-200">Last Seen:</span>
                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capabilities -->
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Device Capabilities</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-qrcode text-{{ $device->supports_qr_scanning ? 'success' : 'slate-400' }}"></i>
                            <span class="text-xs text-slate-600 dark:text-navy-200">QR Code</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-location-dot text-{{ $device->supports_gps ? 'success' : 'slate-400' }}"></i>
                            <span class="text-xs text-slate-600 dark:text-navy-200">GPS</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-camera text-{{ $device->supports_camera ? 'success' : 'slate-400' }}"></i>
                            <span class="text-xs text-slate-600 dark:text-navy-200">Camera</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-wifi text-{{ $device->supports_offline_mode ? 'success' : 'slate-400' }}"></i>
                            <span class="text-xs text-slate-600 dark:text-navy-200">Offline</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Info -->
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Current Status</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="space-y-3">
                        @if($device->is_blocked)
                            <div class="flex items-center space-x-2">
                                <span class="rounded-full bg-error/10 px-2.5 py-1 text-xs+ font-medium text-error">
                                    Blocked
                                </span>
                            </div>
                            @if($device->block_reason)
                                <p class="text-xs text-slate-500 dark:text-navy-300">
                                    Reason: {{ $device->block_reason }}
                                </p>
                            @endif
                        @elseif($device->isLocked())
                            <div class="flex items-center space-x-2">
                                <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                    Locked
                                </span>
                            </div>
                        @elseif(!$device->is_active)
                            <div class="flex items-center space-x-2">
                                <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                    Pending Approval
                                </span>
                            </div>
                        @elseif($device->is_trusted)
                            <div class="flex items-center space-x-2">
                                <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                    Trusted
                                </span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                <span class="rounded-full bg-info/10 px-2.5 py-1 text-xs+ font-medium text-info">
                                    Active
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAutoCheckoutHours(checkbox) {
        const container = document.getElementById('auto_checkout_hours_container');
        if (checkbox.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }
</script>
@endsection