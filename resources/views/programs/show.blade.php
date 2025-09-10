@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $program->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Program Code: {{ $program->program_code }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('programs.edit', $program) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                Edit Program
            </a>
            <a href="{{ route('programs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Back to Programs
            </a>
        </div>
    </div>

    <!-- Status and Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                    <div class="flex items-center mt-1">
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                'inactive' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$program->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($program->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Enrollment</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        {{ $program->enrolled_count ?? 0 }} / {{ $program->max_learners }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Remaining Spots -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining Spots</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        {{ isset($stats['remaining_spots']) ? $stats['remaining_spots'] : ($program->max_learners - ($program->enrolled_count ?? 0)) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Daily Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Rate</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                        R {{ number_format($program->daily_rate, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="space-y-4">
                    @if($program->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->description }}</dd>
                        </div>
                    @endif
                    
                    @if($program->coordinator)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Coordinator</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $program->coordinator->first_name }} {{ $program->coordinator->last_name }}
                                <span class="text-gray-500">({{ $program->coordinator->email }})</span>
                            </dd>
                        </div>
                    @endif
                    
                    @if($program->creator)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $program->creator->first_name }} {{ $program->creator->last_name }}
                                <span class="text-gray-500">on {{ $program->created_at->format('M j, Y') }}</span>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Qualification Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Qualification Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NQF Level</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">Level {{ $program->nqf_level }}</dd>
                    </div>
                    
                    @if($program->saqa_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SAQA ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->saqa_id }}</dd>
                        </div>
                    @endif
                    
                    @if($program->qualification_title)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Qualification Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->qualification_title }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Duration & Schedule -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Duration & Schedule</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->duration_months }} months</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($program->start_date)->format('M j, Y') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($program->end_date)->format('M j, Y') }}</dd>
                    </div>
                    
                    @if($program->duration_weeks)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration (Weeks)</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->duration_weeks }} weeks</dd>
                        </div>
                    @endif
                    
                    @if($program->total_training_days)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Training Days</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->total_training_days }} days</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Financial Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Rate</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">R {{ number_format($program->daily_rate, 2) }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Frequency</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($program->payment_frequency) }}</dd>
                    </div>
                    
                    @if($program->monthly_stipend)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Stipend</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">R {{ number_format($program->monthly_stipend, 2) }}</dd>
                        </div>
                    @endif
                    
                    @if($program->transport_allowance)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Transport Allowance</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">R {{ number_format($program->transport_allowance, 2) }}</dd>
                        </div>
                    @endif
                    
                    @if($program->meal_allowance)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meal Allowance</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">R {{ number_format($program->meal_allowance, 2) }}</dd>
                        </div>
                    @endif
                    
                    @if($program->accommodation_allowance)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Accommodation Allowance</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">R {{ number_format($program->accommodation_allowance, 2) }}</dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('update', $program)
                        <a href="{{ route('programs.edit', $program) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 block text-center">
                            Edit Program
                        </a>
                    @endcan
                    
                    @can('manage', $program)
                        @if($program->status === 'draft' && $program->is_approved)
                            <form action="{{ route('programs.activate', $program) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    Activate Program
                                </button>
                            </form>
                        @elseif($program->status === 'active')
                            <form action="{{ route('programs.deactivate', $program) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    Deactivate Program
                                </button>
                            </form>
                        @endif
                    @endcan
                    
                    @can('approve', $program)
                        @if(!$program->is_approved)
                            <form action="{{ route('programs.approve', $program) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    Approve Program
                                </button>
                            </form>
                        @endif
                    @endcan
                    
                    @can('create', App\Models\Program::class)
                        <form action="{{ route('programs.duplicate', $program) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Duplicate Program
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <!-- Location & Venue -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location & Venue</h3>
                <div class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($program->location_type) }}</dd>
                    </div>
                    
                    @if($program->venue)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->venue }}</dd>
                        </div>
                    @endif
                    
                    @if($program->venue_address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $program->venue_address }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Compliance -->
            @if($program->eti_eligible_program || $program->section_12h_eligible)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SA Compliance</h3>
                    <div class="space-y-3">
                        @if($program->eti_eligible_program)
                            <div class="flex items-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    ETI Eligible
                                </span>
                            </div>
                        @endif
                        
                        @if($program->section_12h_eligible)
                            <div class="flex items-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                    Section 12H Eligible
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Approval Info -->
            @if($program->is_approved && $program->approved_by)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Approval Info</h3>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved By</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $program->approvedBy->first_name }} {{ $program->approvedBy->last_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $program->approved_at->format('M j, Y g:i A') }}
                            </dd>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Training Schedules -->
    @if(isset($schedules) && $schedules->count() > 0)
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Training Schedules</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Session</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $schedule->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($schedule->session_date)->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $scheduleStatusClasses = [
                                                'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $scheduleStatusClasses[$schedule->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <a href="{{ route('schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                            View
                                        </a>
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
@endsection