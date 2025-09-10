@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page header -->
    <div class="flex items-center justify-between py-5 lg:py-6">
        <div class="flex items-center space-x-1">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Training Programs
            </h2>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('programs.create') }}" 
               class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                <i class="fa-solid fa-plus mr-2"></i>
                Create Program
            </a>
        </div>
    </div>

    @if(isset($message))
        <div class="alert flex space-x-2 rounded-lg bg-warning/10 px-4 py-4 text-warning dark:bg-warning/15">
            <i class="fa-solid fa-exclamation-triangle text-base"></i>
            <div>
                <p class="text-xs+ font-medium">Notice</p>
                <p class="text-xs">{{ $message }}</p>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
        <div class="rounded-lg bg-white p-4 shadow dark:bg-navy-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs+ font-medium uppercase tracking-wide text-slate-400 dark:text-navy-300">
                        Total Programs
                    </p>
                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                        {{ $stats['total'] }}
                    </p>
                </div>
                <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow dark:bg-navy-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs+ font-medium uppercase tracking-wide text-slate-400 dark:text-navy-300">
                        Active
                    </p>
                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                        {{ $stats['active'] }}
                    </p>
                </div>
                <div class="flex size-12 items-center justify-center rounded-full bg-success/10 text-success">
                    <i class="fa-solid fa-play text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow dark:bg-navy-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs+ font-medium uppercase tracking-wide text-slate-400 dark:text-navy-300">
                        Draft
                    </p>
                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                        {{ $stats['draft'] }}
                    </p>
                </div>
                <div class="flex size-12 items-center justify-center rounded-full bg-warning/10 text-warning">
                    <i class="fa-solid fa-edit text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow dark:bg-navy-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs+ font-medium uppercase tracking-wide text-slate-400 dark:text-navy-300">
                        Completed
                    </p>
                    <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                        {{ $stats['completed'] }}
                    </p>
                </div>
                <div class="flex size-12 items-center justify-center rounded-full bg-info/10 text-info">
                    <i class="fa-solid fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <!-- Search -->
                <div class="flex-1 min-w-48">
                    <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Search</label>
                    <input name="search" value="{{ request('search') }}" 
                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" 
                           placeholder="Search programs, codes, descriptions..." />
                </div>

                <!-- Status Filter -->
                <div class="min-w-32">
                    <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">Status</label>
                    <select name="status" 
                            class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- ETI Eligible Filter -->
                <div class="min-w-32">
                    <label class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">ETI Eligible</label>
                    <select name="eti_eligible" 
                            class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                        <option value="">All Programs</option>
                        <option value="true" {{ request('eti_eligible') === 'true' ? 'selected' : '' }}>ETI Eligible</option>
                        <option value="false" {{ request('eti_eligible') === 'false' ? 'selected' : '' }}>Not ETI Eligible</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'eti_eligible']))
                        <a href="{{ route('programs.index') }}" class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Programs Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-navy-500">
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Program
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Duration
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Learners
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Status
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Financial
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                    @forelse($programs as $program)
                        <tr class="border-transparent hover:border-slate-200 dark:hover:border-navy-500">
                            <td class="px-4 py-3 lg:px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="flex size-8 items-center justify-center rounded-full text-xs+ font-medium text-white" 
                                         style="background-color: {{ $program->getStatusColor() === 'green' ? '#10b981' : ($program->getStatusColor() === 'red' ? '#ef4444' : ($program->getStatusColor() === 'yellow' ? '#f59e0b' : '#6b7280')) }}">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                            {{ $program->title }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            {{ $program->program_code }}
                                        </p>
                                        @if($program->qualification_title)
                                            <p class="text-xs text-slate-400 dark:text-navy-300">
                                                NQF {{ $program->nqf_level }} - {{ $program->qualification_title }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 lg:px-5">
                                <div class="text-sm">
                                    @if($program->duration_months)
                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                            {{ $program->duration_months }} months
                                        </p>
                                    @endif
                                    @if($program->total_training_days)
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            {{ $program->total_training_days }} training days
                                        </p>
                                    @endif
                                    @if($program->start_date && $program->end_date)
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            {{ $program->start_date->format('M d, Y') }} - {{ $program->end_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 lg:px-5">
                                <div class="text-sm">
                                    <p class="font-medium text-slate-700 dark:text-navy-100">
                                        {{ $program->enrolled_count ?? 0 }} / {{ $program->max_learners }}
                                    </p>
                                    <div class="w-full bg-slate-200 rounded-full h-2 dark:bg-navy-500 mt-1">
                                        <div class="bg-primary h-2 rounded-full" 
                                             style="width: {{ $program->max_learners > 0 ? (($program->enrolled_count ?? 0) / $program->max_learners) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 lg:px-5">
                                <div class="flex flex-col space-y-1">
                                    @if($program->status === 'draft')
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs+ font-medium text-slate-600 dark:bg-navy-500 dark:text-navy-100">
                                            Draft
                                        </span>
                                    @elseif($program->status === 'pending_approval')
                                        <span class="rounded-full bg-warning/10 px-2.5 py-1 text-xs+ font-medium text-warning">
                                            Pending Approval
                                        </span>
                                    @elseif($program->status === 'approved')
                                        <span class="rounded-full bg-info/10 px-2.5 py-1 text-xs+ font-medium text-info">
                                            Approved
                                        </span>
                                    @elseif($program->status === 'active')
                                        <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs+ font-medium text-success">
                                            Active
                                        </span>
                                    @elseif($program->status === 'completed')
                                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs+ font-medium text-primary">
                                            Completed
                                        </span>
                                    @else
                                        <span class="rounded-full bg-error/10 px-2.5 py-1 text-xs+ font-medium text-error">
                                            {{ ucfirst($program->status) }}
                                        </span>
                                    @endif
                                    
                                    @if($program->eti_eligible_program)
                                        <span class="rounded-full bg-success/10 px-2.5 py-0.5 text-xs font-medium text-success">
                                            ETI
                                        </span>
                                    @endif
                                    
                                    @if($program->section_12h_eligible)
                                        <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">
                                            12H
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 lg:px-5">
                                <div class="text-sm">
                                    @if($program->daily_rate)
                                        <p class="font-medium text-slate-700 dark:text-navy-100">
                                            R{{ number_format($program->daily_rate, 2) }}/day
                                        </p>
                                    @endif
                                    @if($program->monthly_stipend)
                                        <p class="text-xs text-slate-400 dark:text-navy-300">
                                            R{{ number_format($program->monthly_stipend, 2) }}/month
                                        </p>
                                    @endif
                                    @if($program->getTotalValue() > 0)
                                        <p class="text-xs text-success font-medium">
                                            Total: R{{ number_format($program->getTotalValue(), 2) }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 lg:px-5">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('programs.show', $program) }}" 
                                       class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                       title="View Program">
                                        <i class="fa-solid fa-eye text-slate-500 dark:text-navy-300"></i>
                                    </a>
                                    
                                    @can('update', $program)
                                        <a href="{{ route('programs.edit', $program) }}" 
                                           class="btn size-8 rounded-full p-0 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25"
                                           title="Edit Program">
                                            <i class="fa-solid fa-edit text-primary"></i>
                                        </a>
                                    @endcan

                                    @can('manage', $program)
                                        @if($program->status === 'approved' && !$program->isActive())
                                            <form method="POST" action="{{ route('programs.activate', $program) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn size-8 rounded-full p-0 hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                        title="Activate Program">
                                                    <i class="fa-solid fa-play text-success"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($program->isActive())
                                            <form method="POST" action="{{ route('programs.deactivate', $program) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn size-8 rounded-full p-0 hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                                        title="Deactivate Program">
                                                    <i class="fa-solid fa-pause text-warning"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    <form method="POST" action="{{ route('programs.duplicate', $program) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn size-8 rounded-full p-0 hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                                title="Duplicate Program">
                                            <i class="fa-solid fa-copy text-info"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <i class="fa-solid fa-graduation-cap text-4xl text-slate-300 dark:text-navy-400"></i>
                                    <p class="text-slate-600 dark:text-navy-200">No programs found</p>
                                    @if(request()->hasAny(['search', 'status', 'eti_eligible']))
                                        <a href="{{ route('programs.index') }}" class="text-primary hover:text-primary-focus">
                                            Clear filters
                                        </a>
                                    @else
                                        <a href="{{ route('programs.create') }}" class="text-primary hover:text-primary-focus">
                                            Create your first program
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                {{ $programs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection