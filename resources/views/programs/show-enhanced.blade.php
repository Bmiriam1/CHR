<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $program->title }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $program->program_code }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('programs.edit', $program) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Edit Program
                </a>
                <a href="{{ route('programs.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Back to Programs
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Program Status Alert -->
            @if(!$program->is_approved)
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Program Pending Approval</h3>
                            <p class="mt-1 text-sm text-yellow-700">This program requires approval before it can be activated.</p>
                        </div>
                    </div>
                </div>
            @elseif($program->status !== 'active')
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Program Inactive</h3>
                            <p class="mt-1 text-sm text-red-700">This program is currently {{ $program->status }} and not accepting enrollments.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Enrollment Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Enrollment</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $program->enrolled_count ?? 0 }}/{{ $program->max_learners }}</p>
                        </div>
                    </div>
                </div>

                <!-- Daily Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Rate</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->daily_rate, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Attendance Rate</p>
                            @php
                                $attendanceRate = 87; // This would be calculated from actual attendance data
                            @endphp
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $attendanceRate }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-200',
                                    'draft' => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200',
                                    'completed' => 'bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-200',
                                    'cancelled' => 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-200'
                                ];
                            @endphp
                            <div class="w-8 h-8 {{ $statusColors[$program->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200' }} rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst($program->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 px-6" role="tablist">
                        <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active" 
                                onclick="switchTab('overview')" 
                                data-tab="overview">
                            Overview
                        </button>
                        <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                onclick="switchTab('schedules')" 
                                data-tab="schedules">
                            Schedules & Attendance
                        </button>
                        <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                onclick="switchTab('participants')" 
                                data-tab="participants">
                            Participants
                        </button>
                        <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                onclick="switchTab('financials')" 
                                data-tab="financials">
                            Financials
                        </button>
                        <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                onclick="switchTab('compliance')" 
                                data-tab="compliance">
                            Compliance
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Overview Tab -->
                    <div id="overview-tab" class="tab-content">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Program Details -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Program Information</h3>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->description ?: 'No description provided' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program Type</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->programType->name ?? 'Not specified' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NQF Level</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Level {{ $program->nqf_level }}</dd>
                                        </div>
                                        @if($program->qualification_title)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Qualification</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->qualification_title }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Duration & Timeline</h3>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->duration_months }} months ({{ $program->duration_weeks }} weeks)</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Training Days</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->total_training_days }} days</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->start_date ? $program->start_date->format('M j, Y') : 'Not set' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->end_date ? $program->end_date->format('M j, Y') : 'Not set' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Location & Contacts -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Location & Venue</h3>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location Type</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($program->location_type) }}</dd>
                                        </div>
                                        @if($program->venue)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->venue }}</dd>
                                        </div>
                                        @endif
                                        @if($program->venue_address)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->venue_address }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Program Team</h3>
                                    <dl class="space-y-4">
                                        @if($program->coordinator)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Coordinator</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $program->coordinator->name }}
                                                <span class="text-gray-500 dark:text-gray-400">({{ $program->coordinator->email }})</span>
                                            </dd>
                                        </div>
                                        @endif
                                        @if($program->creator)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $program->creator->name }}
                                                <span class="text-gray-500 dark:text-gray-400">on {{ $program->created_at->format('M j, Y') }}</span>
                                            </dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>

                                @if($program->is_approved && $program->approvedBy)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Approval Information</h3>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved By</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->approvedBy->name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approval Date</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->approved_at->format('M j, Y g:i A') }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Schedules & Attendance Tab -->
                    <div id="schedules-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <!-- Schedule Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Sessions</p>
                                            @php
                                                $scheduleCount = $program->schedules()->count();
                                            @endphp
                                            <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $scheduleCount }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Completed</p>
                                            @php
                                                $completedCount = $program->schedules()->where('status', 'completed')->count();
                                            @endphp
                                            <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $completedCount }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Upcoming</p>
                                            @php
                                                $upcomingCount = $program->schedules()->where('status', 'scheduled')->where('session_date', '>=', now())->count();
                                            @endphp
                                            <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">{{ $upcomingCount }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule List -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Training Sessions</h3>
                                        <a href="{{ route('schedules.create', ['program' => $program]) }}" 
                                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            Add Session
                                        </a>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @php
                                        $schedules = $program->schedules()->with(['attendanceRecords'])->orderBy('session_date')->get();
                                    @endphp

                                    @if($schedules->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Session</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attendance</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($schedules as $schedule)
                                                        @php
                                                            $attendanceCount = $schedule->attendanceRecords()->whereNotNull('check_in_time')->count();
                                                            $totalLearners = $program->enrolled_count ?? 0;
                                                            $attendanceRate = $totalLearners > 0 ? round(($attendanceCount / $totalLearners) * 100) : 0;
                                                        @endphp
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                {{ $schedule->title ?? 'Session ' . $loop->iteration }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $schedule->session_date ? $schedule->session_date->format('M j, Y') : 'Not scheduled' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') : '' }} - 
                                                                {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') : '' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                <div class="flex items-center">
                                                                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $attendanceCount }}/{{ $totalLearners }}</span>
                                                                    <span class="ml-2 text-xs {{ $attendanceRate >= 80 ? 'text-green-600' : ($attendanceRate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                                        ({{ $attendanceRate }}%)
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @php
                                                                    $statusClasses = [
                                                                        'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                                                        'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                                                    ];
                                                                @endphp
                                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$schedule->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                <div class="flex space-x-2">
                                                                    <a href="{{ route('schedules.show', $schedule) }}" 
                                                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">View</a>
                                                                    <a href="{{ route('schedules.edit', $schedule) }}" 
                                                                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-200">Edit</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-12">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No sessions scheduled</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first training session.</p>
                                            <div class="mt-6">
                                                <a href="{{ route('schedules.create', ['program' => $program]) }}" 
                                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Create Session
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participants Tab -->
                    <div id="participants-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <!-- Participant Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Enrolled</h4>
                                    <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $program->enrolled_count ?? 0 }}</p>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-green-600 dark:text-green-400">Active</h4>
                                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $program->enrolled_count ?? 0 }}</p>
                                </div>
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-yellow-600 dark:text-yellow-400">At Risk</h4>
                                    <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">2</p>
                                </div>
                                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-red-600 dark:text-red-400">Dropped Out</h4>
                                    <p class="text-2xl font-semibold text-red-900 dark:text-red-100">1</p>
                                </div>
                            </div>

                            <!-- Participant List -->
                            @php
                                $learners = \App\Models\User::where('is_learner', true)
                                    ->whereHas('programLearners', function($query) use ($program) {
                                        $query->where('program_id', $program->id);
                                    })
                                    ->with(['attendanceRecords' => function($query) use ($program) {
                                        $query->where('program_id', $program->id);
                                    }])
                                    ->get();
                            @endphp

                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Program Participants</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    @if($learners->count() > 0)
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-800">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Learner</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attendance Rate</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($learners as $learner)
                                                    @php
                                                        $attendedSessions = $learner->attendanceRecords->where('status', 'present')->count();
                                                        $totalSessions = $schedules->count();
                                                        $attendanceRate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center">
                                                                    <span class="text-sm font-medium text-indigo-800 dark:text-indigo-200">
                                                                        {{ substr($learner->name, 0, 1) }}
                                                                    </span>
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $learner->name }}</div>
                                                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $learner->id_number ?? 'Not provided' }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $learner->email }}</div>
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $learner->phone ?? 'No phone' }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $attendanceRate }}%</span>
                                                                <div class="ml-2 w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                                    <div class="h-2 rounded-full {{ $attendanceRate >= 80 ? 'bg-green-500' : ($attendanceRate >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                                                         style="width: {{ $attendanceRate }}%"></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @php
                                                                $status = $attendanceRate >= 80 ? 'active' : ($attendanceRate >= 60 ? 'at-risk' : 'poor');
                                                                $statusClasses = [
                                                                    'active' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                                    'at-risk' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                                    'poor' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                                                ];
                                                            @endphp
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$status] }}">
                                                                {{ ucfirst(str_replace('-', ' ', $status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <a href="{{ route('users.show', $learner) }}" 
                                                               class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">View Profile</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center py-12">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No participants enrolled</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Learners will appear here once they enroll in the program.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financials Tab -->
                    <div id="financials-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <!-- Financial Summary Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Rate</h4>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->daily_rate, 2) }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Program Value</h4>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->getTotalValue(), 2) }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Stipend</h4>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->monthly_stipend ?? 0, 2) }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Allowances</h4>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->getDailyAllowances(), 2) }}</p>
                                </div>
                            </div>

                            <!-- Allowances Breakdown -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Allowances Breakdown</h3>
                                </div>
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Transport Allowance</dt>
                                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->transport_allowance ?? 0, 2) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meal Allowance</dt>
                                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->meal_allowance ?? 0, 2) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Accommodation Allowance</dt>
                                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->accommodation_allowance ?? 0, 2) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Other Allowance</dt>
                                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">R{{ number_format($program->other_allowance ?? 0, 2) }}</dd>
                                        </div>
                                        @if($program->other_allowance_description)
                                        <div class="md:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Other Allowance Description</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->other_allowance_description }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment Schedule</h3>
                                </div>
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Frequency</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($program->payment_frequency ?? 'monthly') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Day</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $program->payment_day_of_month ?? 'End of month' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Daily Payment</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">R{{ number_format($program->getTotalDailyPayment(), 2) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Tab -->
                    <div id="compliance-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <!-- Compliance Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- ETI Status -->
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">ETI Compliance</h3>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $program->eti_eligible_program ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                            {{ $program->eti_eligible_program ? 'Eligible' : 'Not Eligible' }}
                                        </span>
                                    </div>
                                    @if($program->eti_eligible_program)
                                        <div class="mt-4 space-y-2">
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ETI Category</dt>
                                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $program->eti_category ?? 'Not specified' }}</dd>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Section 12H Status -->
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Section 12H</h3>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $program->section_12h_eligible ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                                            {{ $program->section_12h_eligible ? 'Eligible' : 'Not Eligible' }}
                                        </span>
                                    </div>
                                    @if($program->section_12h_eligible)
                                        <div class="mt-4 space-y-2">
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contract Number</dt>
                                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $program->section_12h_contract_number ?? 'Not provided' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Allowance</dt>
                                                <dd class="text-sm text-gray-900 dark:text-gray-100">R{{ number_format($program->section_12h_allowance ?? 0, 2) }}</dd>
                                            </div>
                                            @if($program->section_12h_start_date)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</dt>
                                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $program->section_12h_start_date->format('M j, Y') }} - {{ $program->section_12h_end_date ? $program->section_12h_end_date->format('M j, Y') : 'Ongoing' }}
                                                </dd>
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- BBBEE Information -->
                            @if($program->bbbee_category)
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">BBBEE Information</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">BBBEE Category</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $program->bbbee_category }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client Hosting</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $program->is_client_hosting ? 'Yes' : 'No' }}</dd>
                                    </div>
                                    @if($program->specific_requirements)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specific Requirements</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $program->specific_requirements }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                            @endif

                            <!-- Qualification Details -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Qualification Details</h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NQF Level</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">Level {{ $program->nqf_level }}</dd>
                                    </div>
                                    @if($program->saqa_id)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SAQA ID</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $program->saqa_id }}</dd>
                                    </div>
                                    @endif
                                    @if($program->qualification_title)
                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Qualification Title</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $program->qualification_title }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active state from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });
            
            // Show selected tab content
            document.getElementById(`${tabName}-tab`).classList.remove('hidden');
            
            // Add active state to selected tab button
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            activeButton.classList.add('active', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }

        // Initialize first tab as active
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('overview');
        });
    </script>
</x-app-layout>