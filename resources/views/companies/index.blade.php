@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Client Management
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Manage your client companies and their programs
                    </p>
                </div>
                <div>
                    <a href="{{ route('companies.create') }}"
                       class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Add Sub-Client
                    </a>
                </div>
            </div>
            <!-- Primary Client Stats -->            
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
                <!-- Primary Client -->
                <div class="card lg:col-span-2">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100">{{ $rootCompany->name }}</h3>
                                <p class="text-xs text-primary">Primary Client</p>
                                @if($rootCompany->trading_name && $rootCompany->trading_name !== $rootCompany->name)
                                    <p class="text-xs text-slate-400 dark:text-navy-300">Trading as: {{ $rootCompany->trading_name }}</p>
                                @endif
                            </div>
                            <div class="mask is-squircle flex size-12 items-center justify-center bg-primary/10">
                                <i class="fa fa-building text-primary text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="grid grid-cols-2 gap-4">
                                @if($rootCompany->email)
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Email</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $rootCompany->email }}</p>
                                    </div>
                                @endif
                                @if($rootCompany->phone)
                                    <div>
                                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Phone</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $rootCompany->phone }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('companies.show', $rootCompany) }}"
                                   class="btn bg-primary/10 text-primary hover:bg-primary/20">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Programs -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Programs</p>
                            @php
                                $totalPrograms = 0;
                                foreach($companies as $company) {
                                    $totalPrograms += $company->programs ? $company->programs->count() : 0;
                                }
                            @endphp
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalPrograms }}</h3>
                            <p class="text-xs text-info">All Clients</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-graduation-cap text-info"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Total Learners -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Learners</p>
                            @php
                                $totalLearners = 0;
                                foreach($companies as $company) {
                                    $totalLearners += $company->users ? $company->users->count() : 0;
                                }
                            @endphp
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalLearners }}</h3>
                            <p class="text-xs text-success">Enrolled</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-users text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-Clients Section -->
            @php
                $subClients = $companies->where('parent_company_id', $rootCompany->id);
            @endphp
            
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Sub-Clients
                            <span class="ml-2 badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100">{{ $subClients->count() }}</span>
                        </h2>
                        @if($subClients->count() === 0)
                            <a href="{{ route('companies.create') }}"
                               class="border-b border-dotted border-current pb-0.5 text-xs+ font-medium text-primary outline-none transition-colors duration-300 hover:text-primary/70 focus:text-primary/70">
                                Add your first sub-client
                            </a>
                        @endif
                    </div>

                                <div class="px-4 pb-4 sm:px-5">
                        @if($subClients->count() > 0)
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($subClients as $company)
                                    <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600 hover:bg-slate-200 dark:hover:bg-navy-500 transition-colors">
                                        <!-- Client Header -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-slate-700 dark:text-navy-100">{{ $company->name }}</h4>
                                                @if($company->trading_name && $company->trading_name !== $company->name)
                                                    <p class="text-xs text-slate-400 dark:text-navy-300">Trading as: {{ $company->trading_name }}</p>
                                                @endif
                                            </div>
                                            <span class="badge {{ $company->is_active ? 'bg-success text-white' : 'bg-error text-white' }}">
                                                {{ $company->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>

                                        <!-- Quick Stats -->
                                        <div class="mb-3 grid grid-cols-2 gap-3">
                                            <div class="text-center rounded-lg bg-info/10 p-2">
                                                @php
                                                    $programCount = $company->programs ? $company->programs->count() : 0;
                                                @endphp
                                                <p class="text-lg font-bold text-info">{{ $programCount }}</p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">Programs</p>
                                            </div>
                                            <div class="text-center rounded-lg bg-success/10 p-2">
                                                @php
                                                    $learnerCount = $company->users ? $company->users->count() : 0;
                                                @endphp
                                                <p class="text-lg font-bold text-success">{{ $learnerCount }}</p>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">Learners</p>
                                            </div>
                                        </div>

                                        <!-- Contact Info -->
                                        @if($company->email || $company->phone)
                                        <div class="mb-3 space-y-1">
                                            @if($company->email)
                                                <p class="text-xs text-slate-500 dark:text-navy-400">
                                                    <i class="fa fa-envelope mr-1"></i>
                                                    {{ $company->email }}
                                                </p>
                                            @endif
                                            @if($company->phone)
                                                <p class="text-xs text-slate-500 dark:text-navy-400">
                                                    <i class="fa fa-phone mr-1"></i>
                                                    {{ $company->phone }}
                                                </p>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 dark:border-navy-500">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('companies.show', $company) }}"
                                                   class="text-xs font-medium text-primary hover:text-primary-focus">
                                                    View Programs
                                                </a>
                                                <a href="{{ route('companies.edit', $company) }}"
                                                   class="text-xs font-medium text-warning hover:text-warning-focus">
                                                    Edit
                                                </a>
                                            </div>
                                            
                                            <form action="{{ route('companies.toggleStatus', $company) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn {{ $company->is_active ? 'bg-error/10 text-error hover:bg-error/20' : 'bg-success/10 text-success hover:bg-success/20' }} px-2 py-1 text-xs">
                                                    {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div class="text-slate-400 dark:text-navy-300 mb-4">
                                    <i class="fa fa-building text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-2">No Sub-Clients Yet</h3>
                                <p class="text-slate-500 dark:text-navy-400 mb-6 max-w-sm mx-auto">
                                    Create sub-clients to organize and manage different divisions, branches, or subsidiary companies under {{ $rootCompany->name }}.
                                </p>
                                <a href="{{ route('companies.create') }}"
                                   class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <i class="fa fa-plus mr-2"></i>
                                    Add Your First Sub-Client
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Quick Actions
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <a href="{{ route('programs.index') }}"
                                class="flex items-center justify-center rounded-lg bg-info/10 p-4 transition-colors hover:bg-info/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-graduation-cap text-2xl text-info"></i>
                                    </div>
                                    <p class="text-sm font-medium text-info">View All Programs</p>
                                    <p class="text-xs text-slate-400">Across all clients</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('schedules.index') }}"
                                class="flex items-center justify-center rounded-lg bg-success/10 p-4 transition-colors hover:bg-success/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-calendar text-2xl text-success"></i>
                                    </div>
                                    <p class="text-sm font-medium text-success">View Schedules</p>
                                    <p class="text-xs text-slate-400">Training sessions</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('attendance.index') }}"
                                class="flex items-center justify-center rounded-lg bg-warning/10 p-4 transition-colors hover:bg-warning/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-users text-2xl text-warning"></i>
                                    </div>
                                    <p class="text-sm font-medium text-warning">Attendance Overview</p>
                                    <p class="text-xs text-slate-400">Track learner attendance</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection