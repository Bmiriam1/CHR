@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Generate Payslips
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Generate payslips for employees based on attendance records
                    </p>
                </div>
                <a href="{{ route('payslips.index') }}"
                    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                    <i class="fa fa-arrow-left mr-2"></i>
                    Back to Payslips
                </a>
            </div>

            <form action="{{ route('payslips.generate') }}" method="POST" class="mt-6 space-y-4 sm:space-y-5">
                @csrf

                <!-- Pay Period Information -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Pay Period Information
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 sm:gap-5">
                            <div>
                                <label for="pay_period_start"
                                    class="block text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                    Pay Period Start <span class="text-error">*</span>
                                </label>
                                <label class="relative mt-1.5 flex">
                                    <input type="date" name="pay_period_start" id="pay_period_start"
                                        value="{{ old('pay_period_start', now()->subMonth()->startOfMonth()->format('Y-m-d')) }}"
                                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required />
                                    <span
                                        class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                        <i class="fa fa-calendar text-base"></i>
                                    </span>
                                </label>
                                @error('pay_period_start')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="pay_period_end"
                                    class="block text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                    Pay Period End <span class="text-error">*</span>
                                </label>
                                <label class="relative mt-1.5 flex">
                                    <input type="date" name="pay_period_end" id="pay_period_end"
                                        value="{{ old('pay_period_end', now()->subMonth()->endOfMonth()->format('Y-m-d')) }}"
                                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required />
                                    <span
                                        class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                        <i class="fa fa-calendar text-base"></i>
                                    </span>
                                </label>
                                @error('pay_period_end')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="pay_date" class="block text-sm+ font-medium text-slate-700 dark:text-navy-100">
                                    Pay Date <span class="text-error">*</span>
                                </label>
                                <label class="relative mt-1.5 flex">
                                    <input type="date" name="pay_date" id="pay_date"
                                        value="{{ old('pay_date', now()->format('Y-m-d')) }}"
                                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        required />
                                    <span
                                        class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                        <i class="fa fa-calendar text-base"></i>
                                    </span>
                                </label>
                                @error('pay_date')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payroll Settings -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Payroll Settings
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:gap-5">
                            <div>
                                <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">Default Daily Rate</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">R150.00 per day (based on program
                                    settings)</p>
                            </div>

                            <div>
                                <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">Transport Allowance</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">R50.00 per day worked</p>
                            </div>

                            <div>
                                <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">Meal Allowance</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">R30.00 per day worked</p>
                            </div>

                            <div>
                                <p class="text-sm+ font-medium text-slate-700 dark:text-navy-100">UIF Rate</p>
                                <p class="text-xs+ text-slate-400 dark:text-navy-300">1% of gross pay (capped at R148.72)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Selection -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Employee Selection
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="text-xs+ text-slate-400 dark:text-navy-300 mb-4">
                            Payslips will be generated for all learners in your company for the selected period.
                        </div>

                        @php
                            $learners = \App\Models\User::where('company_id', auth()->user()->company_id)
                                ->whereHas('roles', function ($q) {
                                    $q->where('name', 'learner');
                                })
                                ->get();
                        @endphp

                        @if($learners->count() > 0)
                            <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                                <h3 class="text-sm+ font-medium text-slate-700 dark:text-navy-100 mb-3">Learners to be included:
                                </h3>
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($learners as $learner)
                                        <div class="text-xs+ text-slate-400 dark:text-navy-300">
                                            • {{ $learner->first_name }} {{ $learner->last_name }} ({{ $learner->employee_code }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="rounded-lg border border-warning bg-warning/10 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-exclamation-triangle text-lg text-warning"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm+ font-medium text-warning">No learners found</h3>
                                        <p class="mt-1 text-xs+ text-warning/70">Please ensure you have learners assigned to
                                            your company before generating payslips.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('payslips.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                        <i class="fa fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                        class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90"
                        {{ $learners->count() === 0 ? 'disabled' : '' }}>
                        <i class="fa fa-cog mr-2"></i>
                        Generate Payslips
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.getElementById('pay_period_start');
            const endDateInput = document.getElementById('pay_period_end');
            const payDateInput = document.getElementById('pay_date');

            // Set end date when start date changes
            startDateInput.addEventListener('change', function () {
                if (startDateInput.value && !endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(startDate);
                    endDate.setMonth(endDate.getMonth() + 1);
                    endDate.setDate(0); // Last day of the month
                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            });

            // Set pay date when end date changes
            endDateInput.addEventListener('change', function () {
                if (endDateInput.value && !payDateInput.value) {
                    const endDate = new Date(endDateInput.value);
                    const payDate = new Date(endDate);
                    payDate.setDate(payDate.getDate() + 1);
                    payDateInput.value = payDate.toISOString().split('T')[0];
                }
            });
        });
    </script>
@endsection
