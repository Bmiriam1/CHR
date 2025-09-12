@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Host Locations
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Manage check-in/out locations for learners across programs
                    </p>
                </div>
                <div>
                    <a href="{{ route('hosts.create') }}"
                       class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Add Host Location
                    </a>
                </div>
            </div>

            <!-- Analytics Cards -->
            <div class="mt-6 grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
                <!-- Total Hosts -->            
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['total_hosts'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Locations</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                                    <i class="fa fa-map-marker-alt text-info text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Hosts -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['active_hosts'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Active Locations</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                                    <i class="fa fa-check-circle text-success text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Check-ins -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['total_check_ins_today'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">Today's Check-ins</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                                    <i class="fa fa-clock text-warning text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GPS Enabled -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <div class="card">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $analytics['hosts_with_gps'] }}</p>
                                    <p class="text-xs+ text-slate-400 dark:text-navy-300">GPS Enabled</p>
                                </div>
                                <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                                    <i class="fa fa-satellite text-primary text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Host Locations -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Host Locations
                            <span class="ml-2 badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100">{{ $hosts->count() }}</span>
                        </h2>
                        @if($hosts->count() === 0)
                            <a href="{{ route('hosts.create') }}"
                               class="border-b border-dotted border-current pb-0.5 text-xs+ font-medium text-primary outline-none transition-colors duration-300 hover:text-primary/70 focus:text-primary/70">
                                Add your first host location
                            </a>
                        @endif
                    </div>

                    <div class="px-4 pb-4 sm:px-5">
                        @if($hosts->count() > 0)
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($hosts as $host)
                                    <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600 hover:bg-slate-200 dark:hover:bg-navy-500 transition-colors">
                                        <!-- Host Header -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-slate-700 dark:text-navy-100">{{ $host->name }}</h4>
                                                <p class="text-xs text-slate-400 dark:text-navy-300">{{ $host->program->title ?? 'No Program' }}</p>
                                            </div>
                                            <span class="badge {{ $host->is_active ? 'bg-success text-white' : 'bg-error text-white' }}">
                                                {{ $host->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>

                                        <!-- Location Info -->
                                        <div class="mb-3 space-y-1">
                                            <p class="text-xs text-slate-500 dark:text-navy-400">
                                                <i class="fa fa-map-marker-alt mr-1"></i>
                                                {{ $host->city }}, {{ $host->province }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-navy-400">
                                                <i class="fa fa-home mr-1"></i>
                                                {{ $host->address_line1 }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-navy-400">
                                                <i class="fa fa-circle mr-1"></i>
                                                {{ $host->radius_meters }}m radius
                                            </p>
                                        </div>

                                        <!-- Features -->
                                        <div class="mb-3 flex flex-wrap gap-1">
                                            @if($host->requires_gps_validation)
                                                <span class="badge badge-xs bg-primary text-white">GPS</span>
                                            @endif
                                            @if($host->requires_time_validation)
                                                <span class="badge badge-xs bg-warning text-white">Time</span>
                                            @endif
                                            @if($host->allow_multiple_check_ins)
                                                <span class="badge badge-xs bg-info text-white">Multi</span>
                                            @endif
                                            @if($host->require_supervisor_approval)
                                                <span class="badge badge-xs bg-secondary text-white">Approval</span>
                                            @endif
                                        </div>

                                        <!-- QR Code Info -->
                                        <div class="mb-3 p-2 bg-slate-50 dark:bg-navy-700 rounded text-center">
                                            <div class="text-slate-400 dark:text-navy-300 mb-1">
                                                <i class="fa fa-qrcode text-lg"></i>
                                            </div>
                                            <p class="text-xs font-mono text-slate-600 dark:text-navy-200">{{ $host->qr_code }}</p>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 dark:border-navy-500">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('hosts.show', $host) }}"
                                                   class="text-xs font-medium text-primary hover:text-primary-focus">
                                                    View Details
                                                </a>
                                                <a href="{{ route('hosts.edit', $host) }}"
                                                   class="text-xs font-medium text-warning hover:text-warning-focus">
                                                    Edit
                                                </a>
                                            </div>
                                            
                                            <form action="{{ route('hosts.generate-qr', $host) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn bg-info/10 text-info hover:bg-info/20 px-2 py-1 text-xs">
                                                    <i class="fa fa-sync-alt mr-1"></i>
                                                    Regenerate QR
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
                                    <i class="fa fa-map-marker-alt text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-2">No Host Locations Yet</h3>
                                <p class="text-slate-500 dark:text-navy-400 mb-6 max-w-sm mx-auto">
                                    Create host locations where learners can check in and out. Each location can have GPS validation, time restrictions, and QR codes for easy access.
                                </p>
                                <a href="{{ route('hosts.create') }}"
                                   class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <i class="fa fa-plus mr-2"></i>
                                    Add Your First Host Location
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($hosts->count() > 0)
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Quick Actions
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <a href="{{ route('attendance.index') }}"
                                class="flex items-center justify-center rounded-lg bg-info/10 p-4 transition-colors hover:bg-info/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-users text-2xl text-info"></i>
                                    </div>
                                    <p class="text-sm font-medium text-info">View Attendance</p>
                                    <p class="text-xs text-slate-400">Today's check-ins</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('programs.index') }}"
                                class="flex items-center justify-center rounded-lg bg-success/10 p-4 transition-colors hover:bg-success/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-graduation-cap text-2xl text-success"></i>
                                    </div>
                                    <p class="text-sm font-medium text-success">View Programs</p>
                                    <p class="text-xs text-slate-400">Associated programs</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('hosts.create') }}"
                                class="flex items-center justify-center rounded-lg bg-warning/10 p-4 transition-colors hover:bg-warning/20">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fa fa-plus text-2xl text-warning"></i>
                                    </div>
                                    <p class="text-sm font-medium text-warning">Add Location</p>
                                    <p class="text-xs text-slate-400">New host location</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection