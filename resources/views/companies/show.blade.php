@extends('layouts.app')

@section('content')
<div class="container px-4 sm:px-5">
    <div class="py-4 lg:py-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                    {{ $company->name }}
                </h2>
                <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                    {{ $company->isBranch() ? 'Sub-client of ' . $company->parentCompany->name : 'Primary Client' }}
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $company->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4">
            <!-- Total Programs -->
            <div class="card">
                <div class="flex items-center justify-between p-4">
                    <div>
                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Programs</p>
                        <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['total_programs'] ?? 0) }}</h3>
                        <p class="text-xs text-info">Registered</p>
                    </div>
                    <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                        <i class="fa fa-graduation-cap text-info"></i>
                    </div>
                </div>
            </div>

            <!-- Active Programs -->
            <div class="card">
                <div class="flex items-center justify-between p-4">
                    <div>
                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Active Programs</p>
                        <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['active_programs'] ?? 0) }}</h3>
                        <p class="text-xs text-success">Running</p>
                    </div>
                    <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                        <i class="fa fa-check text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Total Learners -->
            <div class="card">
                <div class="flex items-center justify-between p-4">
                    <div>
                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Learners</p>
                        <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['total_learners'] ?? 0) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-navy-300">With Attendance</p>
                    </div>
                    <div class="mask is-squircle flex size-10 items-center justify-center bg-secondary/10">
                        <i class="fa fa-users text-secondary"></i>
                    </div>
                </div>
            </div>

            <!-- Capacity -->
            <div class="card">
                <div class="flex items-center justify-between p-4">
                    <div>
                        <p class="text-xs+ text-slate-400 dark:text-navy-300">Capacity</p>
                        <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ number_format($stats['remaining_program_capacity'] ?? 0) }}</h3>
                        <p class="text-xs text-slate-400 dark:text-navy-300">Programs Remaining</p>
                    </div>
                    <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                        <i class="fa fa-layer-group text-primary"></i>
                    </div>
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
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('programs.create', ['company_id' => $company->id]) }}"
                           class="flex items-center justify-center rounded-lg bg-primary/10 p-4 transition-colors hover:bg-primary/20">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fa fa-plus text-2xl text-primary"></i>
                                </div>
                                <p class="text-sm font-medium text-primary">Create Program</p>
                                <p class="text-xs text-slate-400">Add a new program for this client</p>
                            </div>
                        </a>
                        <a href="{{ route('companies.edit', $company) }}"
                           class="flex items-center justify-center rounded-lg bg-warning/10 p-4 transition-colors hover:bg-warning/20">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fa fa-edit text-2xl text-warning"></i>
                                </div>
                                <p class="text-sm font-medium text-warning">Edit Client</p>
                                <p class="text-xs text-slate-400">Update client details</p>
                            </div>
                        </a>
                        <a href="{{ route('companies.index') }}"
                           class="flex items-center justify-center rounded-lg bg-secondary/10 p-4 transition-colors hover:bg-secondary/20">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fa fa-arrow-left text-2xl text-secondary"></i>
                                </div>
                                <p class="text-sm font-medium text-secondary">Back to Clients</p>
                                <p class="text-xs text-slate-400">Return to client list</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Section -->
        <div class="mt-6">
            <div class="card">
                <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                    <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Training Programs
                    </h2>
                    <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                       class="btn bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Add Program
                    </a>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    @if($company->programs->count() > 0)
                        <div class="space-y-4">
                            @foreach($company->programs as $program)
                                <div class="flex items-center space-x-4 rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                                    <div class="flex size-12 items-center justify-center rounded-full bg-primary/10">
                                        <i class="fa fa-graduation-cap text-primary"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                <a href="{{ route('programs.show', $program) }}" class="hover:text-primary">
                                                    {{ $program->title }}
                                                </a>
                                            </h3>
                                            @php
                                                $statusClasses = [
                                                    'draft' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-100',
                                                    'active' => 'bg-success/10 text-success',
                                                    'completed' => 'bg-info/10 text-info',
                                                    'cancelled' => 'bg-error/10 text-error'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$program->status] ?? 'bg-slate-100 text-slate-800' }}">
                                                {{ ucfirst($program->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-sm text-slate-500 dark:text-navy-300 mb-3">
                                            <p class="font-mono">{{ $program->program_code }}</p>
                                            @if($program->description)
                                                <p class="mt-1">{{ Str::limit($program->description, 120) }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <p class="text-slate-400 dark:text-navy-300">Daily Rate</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">R{{ number_format($program->daily_rate ?? 0, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-400 dark:text-navy-300">Duration</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    @if($program->start_date && $program->end_date)
                                                        {{ \Carbon\Carbon::parse($program->start_date)->diffInMonths(\Carbon\Carbon::parse($program->end_date)) }} months
                                                    @else
                                                        N/A
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-slate-400 dark:text-navy-300">Schedules</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">{{ $program->schedules->count() }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-400 dark:text-navy-300">Coordinator</p>
                                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $program->coordinator ? $program->coordinator->first_name . ' ' . $program->coordinator->last_name : 'Not assigned' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        @if($program->start_date || $program->end_date)
                                        <div class="flex items-center space-x-4 mt-3 text-sm text-slate-500 dark:text-navy-300">
                                            @if($program->start_date)
                                                <span>Starts: {{ \Carbon\Carbon::parse($program->start_date)->format('M j, Y') }}</span>
                                            @endif
                                            @if($program->end_date)
                                                <span>Ends: {{ \Carbon\Carbon::parse($program->end_date)->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('programs.show', $program) }}" 
                                           class="btn size-8 rounded-full bg-primary/10 p-0 text-primary hover:bg-primary/20">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('programs.edit', $program) }}" 
                                           class="btn size-8 rounded-full bg-warning/10 p-0 text-warning hover:bg-warning/20">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($program->schedules->count() > 0)
                                            <a href="{{ route('schedules.index', ['program' => $program->id]) }}" 
                                               class="btn size-8 rounded-full bg-success/10 p-0 text-success hover:bg-success/20">
                                                <i class="fa fa-calendar"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State for Programs -->
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-slate-100 dark:bg-navy-600 rounded-full flex items-center justify-center mb-6">
                                <i class="fa fa-graduation-cap text-4xl text-slate-400 dark:text-navy-300"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-2">No Programs Yet</h3>
                            <p class="text-slate-500 dark:text-navy-300 mb-6 max-w-sm mx-auto">
                                Create your first training program for {{ $company->name }} to get started with learner management and scheduling.
                            </p>
                            <a href="{{ route('programs.create', ['company_id' => $company->id]) }}" 
                               class="btn bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                <i class="fa fa-plus mr-2"></i>
                                Create First Program
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="mt-6">
            <div class="card">
                <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                    <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Client Information
                    </h2>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-navy-300 uppercase tracking-wide mb-3">Company Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Company Name</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $company->name }}</dd>
                                </div>
                                @if($company->trading_name && $company->trading_name !== $company->name)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Trading Name</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $company->trading_name }}</dd>
                                </div>
                                @endif
                                @if($company->company_registration_number)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Registration Number</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100 font-mono">{{ $company->company_registration_number }}</dd>
                                </div>
                                @endif
                                @if($company->vat_number)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">VAT Number</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100 font-mono">{{ $company->vat_number }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-navy-300 uppercase tracking-wide mb-3">Contact Details</h3>
                            <dl class="space-y-3">
                                @if($company->email)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Email</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">
                                        <a href="mailto:{{ $company->email }}" class="text-primary hover:text-primary-focus">
                                            {{ $company->email }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                                @if($company->phone)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Phone</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">
                                        <a href="tel:{{ $company->phone }}" class="text-primary hover:text-primary-focus">
                                            {{ $company->phone }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                                @if($company->getFormattedPhysicalAddress())
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Address</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $company->getFormattedPhysicalAddress() }}</dd>
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
@endsection