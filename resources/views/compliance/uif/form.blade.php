@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        UIF Forms
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Generate UIF monthly declarations and annual reconciliations
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

            <!-- UIF Forms -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2">
                <!-- Monthly Declaration -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Monthly Declaration
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-calendar text-primary"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">Monthly</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <p class="text-sm text-slate-500 dark:text-navy-300 mb-4">
                            Generate monthly UIF declarations for submission to the Department of Employment and Labour.
                        </p>

                        <form action="{{ route('compliance.uif.declaration') }}" method="POST" class="space-y-4">
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

                            <!-- Year Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Year <span class="text-error">*</span>
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
                            </div>

                            <!-- Month Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Month <span class="text-error">*</span>
                                </label>
                                <select name="month" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select month...</option>
                                    @for($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}" {{ $month == now()->month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 w-full">
                                <i class="fa fa-download mr-2"></i>
                                Generate Monthly Declaration
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Annual Reconciliation -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Annual Reconciliation
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-chart-bar text-success"></i>
                            <span class="text-sm text-slate-500 dark:text-navy-300">Annual</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <p class="text-sm text-slate-500 dark:text-navy-300 mb-4">
                            Generate annual UIF reconciliation for the full calendar year.
                        </p>

                        <form action="{{ route('compliance.uif.reconciliation') }}" method="POST" class="space-y-4">
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

                            <!-- Year Selection -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Year <span class="text-error">*</span>
                                </label>
                                <select name="year" required
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent">
                                    <option value="">Select year...</option>
                                    @for($year = now()->year - 2; $year <= now()->year; $year++)
                                        <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit"
                                class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90 w-full">
                                <i class="fa fa-download mr-2"></i>
                                Generate Annual Reconciliation
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- UIF Information -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            UIF Information
                        </h2>
                        <span class="badge bg-warning text-white">Unemployment Insurance Fund</span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-lg bg-warning/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Contribution Rates</h3>
                                <ul class="text-sm text-slate-500 dark:text-navy-300 mt-2 space-y-1">
                                    <li>• Employee: 1% of salary</li>
                                    <li>• Employer: 1% of salary</li>
                                    <li>• Total: 2% of salary</li>
                                    <li>• Maximum: R177.12/month</li>
                                </ul>
                            </div>
                            <div class="rounded-lg bg-info/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Submission Requirements</h3>
                                <ul class="text-sm text-slate-500 dark:text-navy-300 mt-2 space-y-1">
                                    <li>• Monthly declarations</li>
                                    <li>• Annual reconciliation</li>
                                    <li>• Due by 7th of following month</li>
                                    <li>• Electronic submission</li>
                                </ul>
                            </div>
                            <div class="rounded-lg bg-success/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Benefits</h3>
                                <ul class="text-sm text-slate-500 dark:text-navy-300 mt-2 space-y-1">
                                    <li>• Unemployment benefits</li>
                                    <li>• Maternity benefits</li>
                                    <li>• Illness benefits</li>
                                    <li>• Death benefits</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

