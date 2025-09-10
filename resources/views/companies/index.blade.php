<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Client Management
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('companies.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Add Sub-Client
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Primary Client Card -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $rootCompany->name }}</h3>
                            <p class="text-indigo-100 mt-1">Primary Client</p>
                            @if($rootCompany->trading_name && $rootCompany->trading_name !== $rootCompany->name)
                                <p class="text-indigo-100 text-sm">Trading as: {{ $rootCompany->trading_name }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $companies->sum(function($company) { return $company->programs->count(); }) }}</p>
                                    <p class="text-indigo-100 text-sm">Total Programs</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $companies->sum(function($company) { return $company->users->count(); }) }}</p>
                                    <p class="text-indigo-100 text-sm">Total Learners</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $companies->where('is_active', true)->count() }}</p>
                                    <p class="text-indigo-100 text-sm">Active Clients</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/10 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-2">Contact Information</h4>
                            <div class="space-y-1 text-indigo-100 text-sm">
                                @if($rootCompany->email)
                                    <p><i class="fas fa-envelope mr-2"></i>{{ $rootCompany->email }}</p>
                                @endif
                                @if($rootCompany->phone)
                                    <p><i class="fas fa-phone mr-2"></i>{{ $rootCompany->phone }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($rootCompany->company_registration_number || $rootCompany->vat_number)
                        <div class="bg-white/10 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-2">Registration Details</h4>
                            <div class="space-y-1 text-indigo-100 text-sm">
                                @if($rootCompany->company_registration_number)
                                    <p>Reg: {{ $rootCompany->company_registration_number }}</p>
                                @endif
                                @if($rootCompany->vat_number)
                                    <p>VAT: {{ $rootCompany->vat_number }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($rootCompany->getFormattedPhysicalAddress())
                        <div class="bg-white/10 rounded-lg p-4">
                            <h4 class="font-semibold text-white mb-2">Address</h4>
                            <p class="text-indigo-100 text-sm">{{ $rootCompany->getFormattedPhysicalAddress() }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('companies.show', $rootCompany) }}" 
                           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            View Programs & Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sub-Clients Section -->
            @php
                $subClients = $companies->where('parent_company_id', $rootCompany->id);
            @endphp
            
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Sub-Clients 
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-normal">({{ $subClients->count() }})</span>
                    </h3>
                    @if($subClients->count() === 0)
                        <a href="{{ route('companies.create') }}" 
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm font-medium">
                            Add your first sub-client →
                        </a>
                    @endif
                </div>
            </div>

            @if($subClients->count() > 0)
                <!-- Sub-Clients Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($subClients as $company)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- Client Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $company->name }}</h4>
                                        @if($company->trading_name && $company->trading_name !== $company->name)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Trading as: {{ $company->trading_name }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $company->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Quick Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $company->programs->count() }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Programs</p>
                                    </div>
                                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $company->users->count() }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Learners</p>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                @if($company->email || $company->phone)
                                <div class="mb-4 space-y-1">
                                    @if($company->email)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            {{ $company->email }}
                                        </p>
                                    @endif
                                    @if($company->phone)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                            {{ $company->phone }}
                                        </p>
                                    @endif
                                </div>
                                @endif

                                <!-- Registration Info -->
                                @if($company->company_registration_number)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Reg: {{ $company->company_registration_number }}
                                    </p>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('companies.show', $company) }}" 
                                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm font-medium">
                                            View Programs
                                        </a>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <a href="{{ route('companies.edit', $company) }}" 
                                           class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-200 text-sm font-medium">
                                            Edit
                                        </a>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <form action="{{ route('companies.toggleStatus', $company) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-xs px-2 py-1 rounded {{ $company->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-800 dark:text-red-100' : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-800 dark:text-green-100' }}">
                                                {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Sub-Clients Yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                        Create sub-clients to organize and manage different divisions, branches, or subsidiary companies under {{ $rootCompany->name }}.
                    </p>
                    <a href="{{ route('companies.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Your First Sub-Client
                    </a>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('programs.index') }}" 
                       class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">View All Programs</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Across all clients</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('schedules.index') }}" 
                       class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">View Schedules</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Training sessions</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('attendance.index') }}" 
                       class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Attendance Overview</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Track learner attendance</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>