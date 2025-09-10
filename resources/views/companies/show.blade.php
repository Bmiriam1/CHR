<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $company->name }}
                </h2>
                <div class="flex items-center space-x-3 mt-1">
                    @if($company->isBranch())
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Sub-client of {{ $company->parentCompany->name }}
                        </span>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">Primary Client</span>
                    @endif
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $company->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Create Program
                </a>
                <a href="{{ route('companies.edit', $company) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Edit Client
                </a>
                <a href="{{ route('companies.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Back to Clients
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Client Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Programs Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Programs</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_programs'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Programs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Programs</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['active_programs'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Learners -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Learners</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_learners'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Capacity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Capacity</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['remaining_program_capacity'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">programs remaining</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content - Programs List -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Training Programs</h3>
                                <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                                    Add Program
                                </a>
                            </div>
                        </div>
                        
                        @if($company->programs->count() > 0)
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($company->programs as $program)
                                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        <a href="{{ route('programs.show', $program) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                            {{ $program->title }}
                                                        </a>
                                                    </h4>
                                                    @php
                                                        $statusClasses = [
                                                            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
                                                            'active' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                            'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$program->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst($program->status) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                    <p>{{ $program->program_code }}</p>
                                                    @if($program->description)
                                                        <p class="mt-1">{{ Str::limit($program->description, 120) }}</p>
                                                    @endif
                                                </div>
                                                
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Daily Rate</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">R{{ number_format($program->daily_rate, 2) }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Duration</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $program->duration_months }} months</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Enrolled</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $program->enrolled_count ?? 0 }}/{{ $program->max_learners }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 dark:text-gray-400">Sessions</p>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $program->total_sessions ?? 0 }}</p>
                                                    </div>
                                                </div>
                                                
                                                @if($program->start_date || $program->end_date)
                                                <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                                                    @if($program->start_date)
                                                        <span>Starts: {{ $program->start_date->format('M j, Y') }}</span>
                                                    @endif
                                                    @if($program->end_date)
                                                        <span>Ends: {{ $program->end_date->format('M j, Y') }}</span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex flex-col space-y-2 ml-4">
                                                <a href="{{ route('programs.show', $program) }}" 
                                                   class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm font-medium">
                                                    View Details
                                                </a>
                                                <a href="{{ route('programs.edit', $program) }}" 
                                                   class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-200 text-sm font-medium">
                                                    Edit
                                                </a>
                                                @if($program->schedules->count() > 0)
                                                    <a href="{{ route('schedules.index', ['program' => $program->id]) }}" 
                                                       class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 text-sm font-medium">
                                                        Schedules ({{ $program->schedules->count() }})
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State for Programs -->
                            <div class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Programs Yet</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                                    Create your first training program for {{ $company->name }} to get started with learner management and scheduling.
                                </p>
                                <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create First Program
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar - Client Details -->
                <div class="space-y-6">
                    <!-- Client Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Client Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Company Name</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $company->name }}</dd>
                            </div>
                            @if($company->trading_name && $company->trading_name !== $company->name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Trading Name</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $company->trading_name }}</dd>
                            </div>
                            @endif
                            @if($company->company_registration_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration Number</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $company->company_registration_number }}</dd>
                            </div>
                            @endif
                            @if($company->vat_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">VAT Number</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $company->vat_number }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Contact Information -->
                    @if($company->email || $company->phone)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contact Details</h3>
                        <dl class="space-y-3">
                            @if($company->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                    <a href="mailto:{{ $company->email }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                        {{ $company->email }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            @if($company->phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                    <a href="tel:{{ $company->phone }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                        {{ $company->phone }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    @endif

                    <!-- Address -->
                    @if($company->getFormattedPhysicalAddress())
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Address</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $company->getFormattedPhysicalAddress() }}</p>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                               class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors block text-center">
                                Create Program
                            </a>
                            <a href="{{ route('companies.edit', $company) }}" 
                               class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors block text-center">
                                Edit Client
                            </a>
                            @if($company->programs->count() > 0)
                                <a href="{{ route('schedules.index', ['company' => $company->id]) }}" 
                                   class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors block text-center">
                                    View All Schedules
                                </a>
                                <a href="{{ route('attendance.index', ['company' => $company->id]) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors block text-center">
                                    Attendance Overview
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Capacity & Limits -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Capacity & Limits</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program Capacity</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $stats['total_programs'] }} / {{ $company->max_programs ?? 'Unlimited' }}
                                    @if($company->max_programs)
                                        <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $company->max_programs > 0 ? min(100, ($stats['total_programs'] / $company->max_programs) * 100) : 0 }}%"></div>
                                        </div>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Learner Capacity</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $stats['total_learners'] }} / {{ $company->max_learners ?? 'Unlimited' }}
                                    @if($company->max_learners)
                                        <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $company->max_learners > 0 ? min(100, ($stats['total_learners'] / $company->max_learners) * 100) : 0 }}%"></div>
                                        </div>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>