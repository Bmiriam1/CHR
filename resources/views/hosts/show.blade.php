@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $host->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Host Location Details</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('hosts.edit', $host) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Edit Host
                </a>
                <form action="{{ route('hosts.generateQRCode', $host) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Regenerate QR
                    </button>
                </form>
            </div>
        </div>

        <!-- Host Status Alert -->
        @if(!$host->is_active)
            <div class="mb-6 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Host Location Inactive</h3>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">This host location is currently inactive and
                            cannot be used for check-ins.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Host Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Host Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Host Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                    Basic Information</h4>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Name</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Code</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $host->code }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Program</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">
                                            <a href="{{ route('programs.show', $host->program) }}"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                                {{ $host->program->title ?? 'No Program' }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Status</dt>
                                        <dd>
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $host->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>
                                    @if($host->description)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Description</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->description }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <!-- Location Info -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                    Location Details</h4>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Address</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $host->getFullAddressAttribute() }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">GPS Coordinates</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                            {{ $host->latitude }}, {{ $host->longitude }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Check-in Radius</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->radius_meters }}
                                            meters</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">GPS Validation</dt>
                                        <dd>
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->requires_gps_validation ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                                {{ $host->requires_gps_validation ? 'Required' : 'Optional' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check-in/out Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Check-in/out Settings</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Check-in Settings -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                    Check-in</h4>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Time Validation</dt>
                                        <dd>
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->requires_time_validation ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                                {{ $host->requires_time_validation ? 'Required' : 'Optional' }}
                                            </span>
                                        </dd>
                                    </div>
                                    @if($host->check_in_start_time)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Allowed Hours</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                                {{ $host->check_in_start_time ? \Carbon\Carbon::parse($host->check_in_start_time)->format('H:i') : 'Any time' }}
                                                -
                                                {{ $host->check_in_end_time ? \Carbon\Carbon::parse($host->check_in_end_time)->format('H:i') : 'Any time' }}
                                            </dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Daily Limit</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $host->max_daily_check_ins }} check-ins per day</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Multiple Check-ins
                                        </dt>
                                        <dd>
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->allow_multiple_check_ins ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $host->allow_multiple_check_ins ? 'Allowed' : 'Not Allowed' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Check-out Settings -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                    Check-out</h4>
                                <dl class="space-y-3">
                                    @if($host->check_out_start_time)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Allowed Hours</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                                {{ $host->check_out_start_time ? \Carbon\Carbon::parse($host->check_out_start_time)->format('H:i') : 'Any time' }}
                                                -
                                                {{ $host->check_out_end_time ? \Carbon\Carbon::parse($host->check_out_end_time)->format('H:i') : 'Any time' }}
                                            </dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Supervisor Approval
                                        </dt>
                                        <dd>
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->require_supervisor_approval ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                                {{ $host->require_supervisor_approval ? 'Required' : 'Not Required' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                @if($host->contact_person || $host->contact_phone || $host->contact_email)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @if($host->contact_person)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Contact Person</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->contact_person }}</dd>
                                    </div>
                                @endif
                                @if($host->contact_phone)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Phone</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">
                                            <a href="tel:{{ $host->contact_phone }}"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                                {{ $host->contact_phone }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                @if($host->contact_email)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900 dark:text-white">Email</dt>
                                        <dd class="text-sm text-gray-600 dark:text-gray-300">
                                            <a href="mailto:{{ $host->contact_email }}"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                                {{ $host->contact_email }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - QR Code & Actions -->
            <div class="space-y-6">
                <!-- QR Code Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">QR Code</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <!-- QR Code Display -->
                            <div
                                class="inline-block p-4 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                                <div id="qr-code-container" class="w-48 h-48 mx-auto">
                                    <!-- QR Code would be generated here via JavaScript -->
                                    <div
                                        class="w-full h-full bg-gray-100 dark:bg-gray-600 border-2 border-dashed border-gray-300 dark:border-gray-500 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4h-4.01M12 16v-4" />
                                            </svg>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">QR Code</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                                {{ $host->qr_code }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code Details -->
                            <div class="mt-4 space-y-2">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Code:</span>
                                    <span class="font-mono text-xs">{{ $host->qr_code }}</span>
                                </p>
                                @if($host->qr_code_generated_at)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Generated: {{ $host->qr_code_generated_at->format('M j, Y g:i A') }}
                                    </p>
                                @endif
                            </div>

                            <!-- QR Code Actions -->
                            <div class="mt-6 space-y-3">
                                <button onclick="downloadQRCode()"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Download QR Code
                                </button>
                                <button onclick="printQRCode()"
                                    class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Print QR Code
                                </button>
                                <button onclick="copyQRData()"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Copy QR Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Usage Statistics</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $todayCheckIns = $host->attendanceRecords()
                                ->whereDate('check_in_time', today())
                                ->whereNotNull('check_in_time')
                                ->count();

                            $thisWeekCheckIns = $host->attendanceRecords()
                                ->whereBetween('check_in_time', [now()->startOfWeek(), now()->endOfWeek()])
                                ->whereNotNull('check_in_time')
                                ->count();

                            $totalCheckIns = $host->attendanceRecords()
                                ->whereNotNull('check_in_time')
                                ->count();
                        @endphp

                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-900 dark:text-white">Today</dt>
                                <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $todayCheckIns }}
                                    check-ins</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-900 dark:text-white">This Week</dt>
                                <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $thisWeekCheckIns }}
                                    check-ins</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $totalCheckIns }}
                                    check-ins</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // QR Code functions
        function downloadQRCode() {
            // Implementation would generate and download QR code
            fetch(`{{ route('hosts.downloadQRCode', $host) }}`)
                .then(response => response.json())
                .then(data => {
                    // Create download link
                    const link = document.createElement('a');
                    link.download = 'host-qr-code-{{ $host->code }}.png';
                    // In a real implementation, you'd generate the actual QR code image
                    // link.href = data.qr_code_image_url;
                    // link.click();
                    alert('QR code download functionality would be implemented here');
                })
                .catch(error => {
                    console.error('Error downloading QR code:', error);
                    alert('Error downloading QR code');
                });
        }

        function printQRCode() {
            // Implementation would open print dialog with QR code
            alert('QR code print functionality would be implemented here');
        }

        function copyQRData() {
            navigator.clipboard.writeText('{{ $host->qr_code }}').then(function () {
                alert('QR code data copied to clipboard');
            }, function (err) {
                console.error('Error copying QR code data: ', err);
                alert('Error copying QR code data');
            });
        }
    </script>
@endsection