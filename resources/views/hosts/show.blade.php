@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('hosts.index') }}" class="text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                            {{ $host->name }}
                        </h2>
                        <span class="badge {{ $host->is_active ? 'bg-success text-white' : 'bg-error text-white' }}">
                            {{ $host->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Host location for {{ $host->program->title ?? 'No Program' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('hosts.edit', $host) }}"
                       class="btn bg-warning font-medium text-white hover:bg-warning-focus">
                        <i class="fa fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('hosts.generateQRCode', $host) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn bg-info font-medium text-white hover:bg-info-focus">
                            <i class="fa fa-sync-alt mr-2"></i>Regenerate QR
                        </button>
                    </form>
                </div>
            </div>

            <!-- Analytics Cards -->
            <div class="mt-6 grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['total_check_ins'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Check-ins</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                                    <i class="fa fa-users text-info text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['check_ins_today'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Today</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                                    <i class="fa fa-calendar-day text-success text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['check_ins_this_week'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">This Week</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                                    <i class="fa fa-calendar-week text-warning text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['unique_users'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Unique Users</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                                    <i class="fa fa-user-friends text-primary text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="mt-6 grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
                <!-- Host Information -->
                <div class="col-span-12 lg:col-span-8">
                    <div class="card">
                        <div class="px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">Host Information</h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Host Code</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100 font-mono">{{ $host->code }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Program</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                            @if($host->program)
                                                <a href="{{ route('programs.show', $host->program) }}" class="text-primary hover:text-primary-focus">
                                                    {{ $host->program->title }}
                                                </a>
                                            @else
                                                No Program Assigned
                                            @endif
                                        </p>
                                    </div>
                                    @if($host->description)
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Description</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $host->description }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Address</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $host->full_address }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">GPS & Radius</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100 font-mono">{{ $host->latitude }}, {{ $host->longitude }}</p>
                                        <p class="text-sm text-slate-600 dark:text-navy-200">{{ $host->radius_meters }}m radius</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code & Activity -->
                <div class="col-span-12 lg:col-span-4">
                    <div class="card">
                        <div class="px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">QR Code</h2>
                        </div>
                        <div class="px-4 pb-4 sm:px-5">
                            <div class="text-center">
                                <div class="mx-auto mb-4 flex h-32 w-32 items-center justify-center rounded-lg bg-slate-100 dark:bg-navy-600">
                                    <i class="fa fa-qrcode text-4xl text-slate-400 dark:text-navy-300"></i>
                                </div>
                                <p class="text-xs font-mono text-slate-600 dark:text-navy-200 mb-2">{{ $host->qr_code }}</p>
                                <form action="{{ route('hosts.generateQRCode', $host) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn w-full bg-info/10 text-info hover:bg-info/20">
                                        <i class="fa fa-sync-alt mr-1"></i>Regenerate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
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