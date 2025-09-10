<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $host->name }} - Host Details
            </h2>
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
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Host Status Alert -->
            @if(!$host->is_active)
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Host Location Inactive</h3>
                            <p class="mt-1 text-sm text-red-700">This host location is currently inactive and cannot be used for check-ins.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Host Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Host Information Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Host Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Info -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Basic Information</h4>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Name</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Code</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $host->code }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Program</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">
                                                <a href="{{ route('programs.show', $host->program) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                                    {{ $host->program->title }}
                                                </a>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Status</dt>
                                            <dd>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                    {{ $host->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </dd>
                                        </div>
                                        @if($host->description)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Description</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->description }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>

                                <!-- Location Info -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Location Details</h4>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Address</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->full_address }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">GPS Coordinates</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $host->latitude }}, {{ $host->longitude }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Check-in Radius</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->radius_meters }} meters</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">GPS Validation</dt>
                                            <dd>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->requires_gps_validation ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
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
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Check-in/out Settings</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Check-in Settings -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Check-in</h4>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Time Validation</dt>
                                            <dd>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->requires_time_validation ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                                    {{ $host->requires_time_validation ? 'Required' : 'Optional' }}
                                                </span>
                                            </dd>
                                        </div>
                                        @if($host->check_in_start_time)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Allowed Hours</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                                {{ $host->check_in_start_time ? \Carbon\Carbon::parse($host->check_in_start_time)->format('H:i') : 'Any time' }} - 
                                                {{ $host->check_in_end_time ? \Carbon\Carbon::parse($host->check_in_end_time)->format('H:i') : 'Any time' }}
                                            </dd>
                                        </div>
                                        @endif
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Daily Limit</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->max_daily_check_ins }} check-ins per day</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Multiple Check-ins</dt>
                                            <dd>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->allow_multiple_check_ins ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                    {{ $host->allow_multiple_check_ins ? 'Allowed' : 'Not Allowed' }}
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Check-out Settings -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Check-out</h4>
                                    <dl class="space-y-3">
                                        @if($host->check_out_start_time)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Allowed Hours</dt>
                                            <dd class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                                {{ $host->check_out_start_time ? \Carbon\Carbon::parse($host->check_out_start_time)->format('H:i') : 'Any time' }} - 
                                                {{ $host->check_out_end_time ? \Carbon\Carbon::parse($host->check_out_end_time)->format('H:i') : 'Any time' }}
                                            </dd>
                                        </div>
                                        @endif
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Supervisor Approval</dt>
                                            <dd>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $host->require_supervisor_approval ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
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
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contact Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @if($host->contact_person)
                                <div>
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Contact Person</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300">{{ $host->contact_person }}</dd>
                                </div>
                                @endif
                                @if($host->contact_phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Phone</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300">
                                        <a href="tel:{{ $host->contact_phone }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            {{ $host->contact_phone }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                                @if($host->contact_email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Email</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300">
                                        <a href="mailto:{{ $host->contact_email }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            {{ $host->contact_email }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Attendance Activity -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Check-ins</h3>
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">View All</a>
                            </div>
                        </div>
                        <div class="p-6">
                            @php
                                $recentAttendance = $host->attendanceRecords()
                                    ->with(['user'])
                                    ->whereNotNull('check_in_time')
                                    ->orderBy('check_in_time', 'desc')
                                    ->take(5)
                                    ->get();
                            @endphp

                            @if($recentAttendance->count() > 0)
                                <div class="flow-root">
                                    <ul class="-my-3 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($recentAttendance as $attendance)
                                        <li class="py-3">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-indigo-800 dark:text-indigo-200">
                                                            {{ substr($attendance->user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                        {{ $attendance->user->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $attendance->check_in_time->format('M j, Y g:i A') }}
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                        Checked In
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No recent check-ins at this location</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - QR Code & Actions -->
                <div class="space-y-6">
                    <!-- QR Code Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">QR Code</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <!-- QR Code Display -->
                                <div class="inline-block p-4 bg-white border border-gray-300 rounded-lg">
                                    <div id="qr-code-container" class="w-48 h-48 mx-auto">
                                        <!-- QR Code would be generated here via JavaScript -->
                                        <div class="w-full h-full bg-gray-100 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center">
                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4h-4.01M12 16v-4" />
                                                </svg>
                                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">QR Code</p>
                                                <p class="text-xs text-gray-400 font-mono">{{ $host->qr_code }}</p>
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
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Usage Statistics</h3>
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
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Today</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $todayCheckIns }} check-ins</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">This Week</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $thisWeekCheckIns }} check-ins</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-900 dark:text-gray-100">Total</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300 font-medium">{{ $totalCheckIns }} check-ins</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Test Check-in -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Test Check-in</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                Test the QR code and location validation for this host.
                            </p>
                            <button onclick="testCheckIn()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Test Check-in Process
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Check-in Modal -->
    <div id="test-checkin-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Test Check-in</h3>
                    <button onclick="closeTestModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="test-checkin-form" onsubmit="submitTestCheckIn(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">QR Code</label>
                            <input type="text" 
                                   value="{{ $host->qr_code }}" 
                                   readonly
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm font-mono">
                        </div>
                        
                        @if($host->requires_gps_validation)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Test Location</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" 
                                       step="any" 
                                       placeholder="Latitude" 
                                       id="test-latitude"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                                <input type="number" 
                                       step="any" 
                                       placeholder="Longitude" 
                                       id="test-longitude"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                            </div>
                            <button type="button" 
                                    onclick="useCurrentLocation()" 
                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                Use Current Location
                            </button>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Test Check-in
                        </button>
                        <button type="button" 
                                onclick="closeTestModal()" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
                
                <div id="test-result" class="mt-4 hidden"></div>
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
            navigator.clipboard.writeText('{{ $host->qr_code }}').then(function() {
                alert('QR code data copied to clipboard');
            }, function(err) {
                console.error('Error copying QR code data: ', err);
                alert('Error copying QR code data');
            });
        }

        // Test check-in functions
        function testCheckIn() {
            document.getElementById('test-checkin-modal').classList.remove('hidden');
        }

        function closeTestModal() {
            document.getElementById('test-checkin-modal').classList.add('hidden');
            document.getElementById('test-result').classList.add('hidden');
        }

        function useCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('test-latitude').value = position.coords.latitude;
                    document.getElementById('test-longitude').value = position.coords.longitude;
                }, function(error) {
                    alert('Error getting current location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser');
            }
        }

        function submitTestCheckIn(event) {
            event.preventDefault();
            
            const qrCode = '{{ $host->qr_code }}';
            const latitude = document.getElementById('test-latitude')?.value;
            const longitude = document.getElementById('test-longitude')?.value;
            
            const data = {
                qr_code: qrCode,
                latitude: latitude,
                longitude: longitude
            };

            fetch(`{{ route('hosts.validateQRCode') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('test-result');
                resultDiv.classList.remove('hidden');
                
                if (data.valid) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Check-in Valid</h3>
                                    <p class="mt-1 text-sm text-green-700">The QR code and location are valid for check-in.</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Check-in Invalid</h3>
                                    <p class="mt-1 text-sm text-red-700">${data.error}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error testing check-in:', error);
                const resultDiv = document.getElementById('test-result');
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-md p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <p class="mt-1 text-sm text-red-700">Error testing check-in functionality.</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    </script>
</x-app-layout>