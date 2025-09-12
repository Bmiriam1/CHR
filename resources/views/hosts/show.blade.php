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
                    <form action="{{ route('hosts.generate-qr', $host) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="btn bg-info font-medium text-white hover:bg-info-focus">
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
                                <form action="{{ route('hosts.generate-qr', $host) }}" method="POST">
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