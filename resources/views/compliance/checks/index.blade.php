@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Compliance Checks
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        System validation and compliance monitoring
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

            <!-- Compliance Checks Results -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Compliance Validation Results
                        </h2>
                        <div class="flex items-center space-x-2">
                            <button onclick="runValidation()"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                <i class="fa fa-refresh mr-2"></i>
                                Run Checks
                            </button>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        @if(count($checks) > 0)
                            <div class="space-y-4">
                                @foreach($checks as $check)
                                    <div
                                        class="flex items-center space-x-3 rounded-lg p-4 {{ $check['status'] === 'pass' ? 'bg-success/10' : ($check['status'] === 'warning' ? 'bg-warning/10' : 'bg-error/10') }}">
                                        <div
                                            class="flex size-10 items-center justify-center rounded-full {{ $check['status'] === 'pass' ? 'bg-success' : ($check['status'] === 'warning' ? 'bg-warning' : 'bg-error') }} text-white">
                                            @if($check['status'] === 'pass')
                                                <i class="fa fa-check text-xs"></i>
                                            @elseif($check['status'] === 'warning')
                                                <i class="fa fa-exclamation-triangle text-xs"></i>
                                            @else
                                                <i class="fa fa-times text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $check['check'] }}
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                                {{ $check['message'] }}
                                            </p>
                                            <span
                                                class="badge {{ $check['status'] === 'pass' ? 'bg-success' : ($check['status'] === 'warning' ? 'bg-warning' : 'bg-error') }} text-white text-xs mt-1">
                                                {{ ucfirst($check['status']) }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-slate-400 dark:text-navy-300">
                                                {{ $check['category'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-slate-400 dark:text-navy-300 mb-2">
                                    <i class="fa fa-shield-alt text-4xl"></i>
                                </div>
                                <p class="text-slate-500 dark:text-navy-400">No compliance checks available</p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                    Click "Run Checks" to validate system compliance
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Compliance Categories -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3">
                <!-- Company Setup -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Company Setup
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-building text-info"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <ul class="text-sm text-slate-500 dark:text-navy-300 space-y-2">
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>SARS PAYE Reference</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>UIF Reference Number</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Company Registration</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Tax Compliance Status</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Payroll -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Payroll
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-calculator text-primary"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <ul class="text-sm text-slate-500 dark:text-navy-300 space-y-2">
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Payslip Processing</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Tax Calculations</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>UIF Contributions</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>SDL Contributions</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Attendance
                        </h2>
                        <div class="flex items-center space-x-2">
                            <i class="fa fa-clock text-warning"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <ul class="text-sm text-slate-500 dark:text-navy-300 space-y-2">
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Attendance Records</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Program Enrollment</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>ETI Eligibility</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fa fa-check text-success text-xs"></i>
                                <span>Completion Tracking</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Compliance Tips -->
            <div class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Compliance Tips
                        </h2>
                        <span class="badge bg-info text-white">Best Practices</span>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-lg bg-info/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Regular Monitoring</h3>
                                <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                    Run compliance checks regularly to ensure all systems are properly configured and up to
                                    date.
                                </p>
                            </div>
                            <div class="rounded-lg bg-success/10 p-4">
                                <h3 class="font-medium text-slate-700 dark:text-navy-100">Timely Submissions</h3>
                                <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">
                                    Submit all required returns and declarations on time to avoid penalties and maintain
                                    compliance.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function runValidation() {
            // This would typically make an AJAX request to run validation
            // For now, we'll just reload the page
            window.location.reload();
        }
    </script>
@endsection

