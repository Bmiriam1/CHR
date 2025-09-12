@extends('layouts.app')

@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        EMP501 Annual Reconciliation
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Generate annual PAYE reconciliation for SARS submission
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('compliance.dashboard') }}"
                        class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Current Tax Year -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">{{ now()->month >= 3 ? now()->year : now()->year - 1 }}/{{ now()->month >= 3 ? now()->year + 1 : now()->year }}</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Current Tax Year</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                                <i class="fa fa-calendar-alt text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">May 31</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Submission Due</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                                <i class="fa fa-exclamation-triangle text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Type -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">Annual</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Reconciliation</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                                <i class="fa fa-chart-bar text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Output Format -->
                <div class="card">
                    <div class="p-4">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-700 dark:text-navy-100">Excel</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">Export Format</p>
                            </div>
                            <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                                <i class="fa fa-file-excel text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Generate EMP501 Reconciliation
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-chart-bar text-success"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">Annual Report</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <form action="{{ route('compliance.sars.emp501.generate') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Company Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Company <span class="text-error">*</span>
                                </label>
                                <select name="company_id" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select a company...</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tax Year Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Tax Year <span class="text-error">*</span>
                                </label>
                                <select name="tax_year" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select tax year...</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $year == (now()->month >= 3 ? now()->year : now()->year - 1) ? 'selected' : '' }}>
                                            {{ $year }}/{{ $year + 1 }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tax_year')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Information Box -->
                            <div class="rounded-lg bg-gradient-to-r from-success/5 to-success/10 border border-success/20 p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-success text-white">
                                        <i class="fa fa-info text-xs"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                            About EMP501 Reconciliation
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                                            EMP501 is the annual reconciliation of PAYE, UIF, and SDL contributions.
                                            This report summarizes all employee tax deductions for the tax year (March to
                                            February).
                                        </p>
                                        <ul class="mt-2 text-xs text-slate-500 dark:text-navy-300 space-y-1">
                                            <li class="flex items-center space-x-2">
                                                <i class="fa fa-check text-success text-xs"></i>
                                                <span>Covers full tax year from March to February</span>
                                            </li>
                                            <li class="flex items-center space-x-2">
                                                <i class="fa fa-check text-success text-xs"></i>
                                                <span>Includes individual employee summaries</span>
                                            </li>
                                            <li class="flex items-center space-x-2">
                                                <i class="fa fa-check text-success text-xs"></i>
                                                <span>Generates properly formatted Excel (.xlsx) file</span>
                                            </li>
                                            <li class="flex items-center space-x-2">
                                                <i class="fa fa-check text-success text-xs"></i>
                                                <span>Required for annual tax compliance</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('compliance.dashboard') }}"
                                    class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                                    <i class="fa fa-download mr-2"></i>
                                    Generate EMP501
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tax Year Summary Cards -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="card">
                    <div class="px-4 py-4">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4">Current Tax Year</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">Period</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ now()->month >= 3 ? now()->year : now()->year - 1 }}/{{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">Start Date</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                    March {{ now()->month >= 3 ? now()->year : now()->year - 1 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">End Date</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                    February {{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="px-4 py-4">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4">Submission Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">Due Date</span>
                                <span class="font-medium text-warning">
                                    {{ now()->month >= 3 ? now()->year + 1 : now()->year }}-05-31
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">Format</span>
                                <span class="font-medium text-slate-700 dark:text-navy-100">Excel (.xlsx)</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-navy-300">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info/10 text-info">
                                    Ready to Generate
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

