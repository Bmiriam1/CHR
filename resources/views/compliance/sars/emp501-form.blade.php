@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
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
                            <div class="rounded-lg bg-success/10 p-4">
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
                                            <li>• Covers full tax year from March to February</li>
                                            <li>• Includes individual employee summaries</li>
                                            <li>• Generates properly formatted Excel (.xlsx) file</li>
                                            <li>• Required for annual tax compliance</li>
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

            <!-- Tax Year Information -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Tax Year Information
                        </h2>
                        <span class="badge bg-info text-white">
                            {{ now()->month >= 3 ? now()->year : now()->year - 1 }}/{{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                        </span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Current Tax Year</h3>
                                <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                    {{ now()->month >= 3 ? now()->year : now()->year - 1 }}/{{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                    March {{ now()->month >= 3 ? now()->year : now()->year - 1 }} to February
                                    {{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Submission Deadline</h3>
                                <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                    {{ now()->month >= 3 ? now()->year + 1 : now()->year }}-05-31
                                </p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                    Must be submitted by May 31st
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

