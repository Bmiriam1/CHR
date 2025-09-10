@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        EMP201 Monthly PAYE Return
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Generate monthly PAYE returns for SARS eFiling submission
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
                            Generate EMP201 Return
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-file-alt text-primary"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">SARS Form</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <form action="{{ route('compliance.sars.emp201.generate') }}" method="POST" class="space-y-6">
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

                            <!-- Year Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Tax Year <span class="text-error">*</span>
                                </label>
                                <select name="year" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select year...</option>
                                    @for($year = now()->year - 2; $year <= now()->year + 1; $year++)
                                        <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                @error('year')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Month Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Month <span class="text-error">*</span>
                                </label>
                                <select name="month" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select month...</option>
                                    @foreach($months as $month)
                                        <option value="{{ $month['value'] }}" {{ $month['value'] == now()->month ? 'selected' : '' }}>
                                            {{ $month['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Information Box -->
                            <div class="rounded-lg bg-info/10 p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-info text-white">
                                        <i class="fa fa-info text-xs"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                            About EMP201 Returns
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                                            EMP201 returns must be submitted to SARS by the 7th of the following month. 
                                            This form will generate an Excel file with all required calculations and data.
                                        </p>
                                        <ul class="mt-2 text-xs text-slate-500 dark:text-navy-300 space-y-1">
                                            <li>• Includes PAYE, UIF, SDL, and ETI calculations</li>
                                            <li>• Generates Excel format (.xlsx) for easy viewing and submission</li>
                                            <li>• Automatically calculates net PAYE due after ETI benefits</li>
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
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <i class="fa fa-download mr-2"></i>
                                    Generate EMP201
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent EMP201 Returns -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Recent EMP201 Returns
                        </h2>
                        <span class="badge bg-slate-200 text-slate-600 dark:bg-navy-500 dark:text-navy-100">
                            Last 6 months
                        </span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="text-center py-8">
                            <div class="text-slate-400 dark:text-navy-300 mb-2">
                                <i class="fa fa-file-alt text-4xl"></i>
                            </div>
                            <p class="text-slate-500 dark:text-navy-400">No recent EMP201 returns</p>
                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                Generated returns will appear here for easy reference
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
