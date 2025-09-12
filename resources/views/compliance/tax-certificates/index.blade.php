@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Tax Certificates
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Generate IRP5 and IT3(a) certificates for employees
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

        </div>

        <!-- Certificate Types -->
        <div class="col-span-12 lg:col-span-8">
            <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2">
                <!-- IRP5 Certificates -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            IRP5 Certificates
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-certificate text-warning"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">PAYE</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <p class="text-sm text-slate-500 dark:text-navy-300 mb-4">
                            Generate IRP5 certificates for employees showing PAYE, UIF, and other tax deductions.
                        </p>

                        <form action="{{ route('compliance.tax_certificates.irp5') }}" method="POST" class="space-y-4">
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
                            </div>

                            <!-- Employee Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Employees
                                </label>
                                <select name="user_ids[]" multiple
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">All employees (leave empty for all)</option>
                                    <!-- Employee options would be populated via JavaScript -->
                                </select>
                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                    Leave empty to generate certificates for all employees
                                </p>
                            </div>

                            <div class="space-y-2">
                                <button type="submit"
                                    class="btn bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90 w-full">
                                    <i class="fa fa-file-pdf mr-2"></i>
                                    Generate IRP5 PDF
                                </button>
                                <button type="submit" formaction="{{ route('compliance.tax_certificates.irp5_csv') }}"
                                    class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90 w-full">
                                    <i class="fa fa-file-csv mr-2"></i>
                                    Export IRP5 CSV
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- IT3(a) Certificates -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            IT3(a) Certificates
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-file-alt text-info"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">Interest</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <p class="text-sm text-slate-500 dark:text-navy-300 mb-4">
                            Generate IT3(a) certificates for interest earned on employee investments or savings.
                        </p>

                        <form action="{{ route('compliance.tax_certificates.it3a') }}" method="POST" class="space-y-4">
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
                            </div>

                            <!-- Interest Amount -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Total Interest Amount
                                </label>
                                <input type="number" name="interest_amount" step="0.01" min="0"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent"
                                    placeholder="0.00">
                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                    Total interest earned by all employees
                                </p>
                            </div>

                            <button type="submit"
                                class="btn bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 w-full">
                                <i class="fa fa-download mr-2"></i>
                                Generate IT3(a) Certificates
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Information -->
        <div class="col-span-12 lg:col-span-4">
            <div class="grid grid-cols-1 gap-4 sm:gap-5">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Certificate Information
                        </h2>
                        <span class="badge bg-slate-200 text-slate-600 dark:bg-navy-500 dark:text-navy-100">
                            Tax Year
                            {{ now()->month >= 3 ? now()->year : now()->year - 1 }}/{{ now()->month >= 3 ? now()->year + 1 : now()->year }}
                        </span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5 space-y-4">
                        <div class="rounded-lg bg-warning/10 p-4">
                            <h3 class="font-medium text-slate-700 dark:text-navy-100">IRP5 Certificates</h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                Required for all employees earning above the tax threshold
                            </p>
                            <ul class="text-xs text-slate-400 dark:text-navy-300 mt-2 space-y-1">
                                <li>• Shows PAYE deductions</li>
                                <li>• Includes UIF contributions</li>
                                <li>• CSV format for SARS e@syfile</li>
                                <li>• PDF format for employees</li>
                            </ul>
                        </div>
                        <div class="rounded-lg bg-info/10 p-4">
                            <h3 class="font-medium text-slate-700 dark:text-navy-100">IT3(a) Certificates</h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                Required for interest earned above R23,800
                            </p>
                            <ul class="text-xs text-slate-400 dark:text-navy-300 mt-2 space-y-1">
                                <li>• Shows interest earned</li>
                                <li>• Required for tax returns</li>
                                <li>• SARS reporting requirement</li>
                            </ul>
                        </div>
                        <div class="rounded-lg bg-success/10 p-4">
                            <h3 class="font-medium text-slate-700 dark:text-navy-100">Submission Deadline</h3>
                            <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                Certificates must be issued by May 31st
                            </p>
                            <ul class="text-xs text-slate-400 dark:text-navy-300 mt-2 space-y-1">
                                <li>• Electronic submission to SARS</li>
                                <li>• PDF format for employees</li>
                                <li>• Keep copies for 5 years</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection