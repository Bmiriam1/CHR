@extends('layouts.app')

@section('title', 'New Leave Request')

@section('content')
    <div
        class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        
        <!-- Main Content Area -->
        <div class="col-span-12 lg:col-span-8">
            <!-- Page Header -->
            <div class="flex items-center justify-between space-x-2 mb-6">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-800 line-clamp-1 dark:text-navy-100">
                        New Leave Request
                    </h2>
                    <p class="mt-1 text-xs-plus text-slate-500 dark:text-navy-200">
                        Submit a new leave request following SA employment law guidelines
                    </p>
                </div>
                <a href="{{ route('leave-requests.index') }}"
                    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Leave
                </a>
            </div>

            <!-- Leave Request Form -->
            <div class="card col-span-12">
                <div class="flex items-center justify-between py-3 px-4">
                    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Request Information
                    </h2>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Program Selection -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Program <span class="text-error">*</span>
                                </label>
                                <select name="program_id" required
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('program_id') border-error @enderror">
                                    <option value="">Select a program</option>
                                    @foreach(auth()->user()->programs ?? [] as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Leave Type Selection -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Leave Type <span class="text-error">*</span>
                                </label>
                                <select name="leave_type_id" required
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('leave_type_id') border-error @enderror">
                                    <option value="">Select leave type</option>
                                    @foreach($leaveTypes as $leaveType)
                                        <option value="{{ $leaveType->id }}" 
                                            {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}
                                            data-balance="{{ $balances[$leaveType->id] ?? 0 }}"
                                            data-requires-medical="{{ $leaveType->requires_medical_certificate ? 'true' : 'false' }}"
                                            data-medical-after="{{ $leaveType->medical_cert_required_after_days }}">
                                            {{ $leaveType->name }} 
                                            @if(isset($balances[$leaveType->id]))
                                                ({{ $balances[$leaveType->id] }} days available)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Start Date <span class="text-error">*</span>
                                </label>
                                <input type="date" name="start_date" required
                                    value="{{ old('start_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('start_date') border-error @enderror">
                                @error('start_date')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    End Date <span class="text-error">*</span>
                                </label>
                                <input type="date" name="end_date" required
                                    value="{{ old('end_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('end_date') border-error @enderror">
                                @error('end_date')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Reason <span class="text-error">*</span>
                                </label>
                                <textarea name="reason" required rows="3"
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('reason') border-error @enderror"
                                    placeholder="Please provide a reason for your leave request...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Additional Notes
                                </label>
                                <textarea name="notes" rows="2"
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('notes') border-error @enderror"
                                    placeholder="Any additional information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Medical Certificate Upload -->
                            <div class="sm:col-span-2" id="medical-certificate-section" style="display: none;">
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Medical Certificate
                                </label>
                                <input type="file" name="medical_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder-navy-300 dark:hover:border-navy-400 dark:focus:border-accent @error('medical_certificate') border-error @enderror">
                                <p class="mt-1 text-xs text-slate-500 dark:text-navy-200">
                                    Upload medical certificate (PDF, JPG, PNG - Max 10MB)
                                </p>
                                @error('medical_certificate')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('leave-requests.index') }}"
                                class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                                Cancel
                            </a>
                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-span-12 lg:col-span-4">
            <!-- Leave Guidelines -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-info/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Leave Guidelines
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-navy-200">
                    <div>
                        <h4 class="font-medium text-slate-700 dark:text-navy-100">Annual Leave</h4>
                        <p>21 days per year, requires 14 days notice</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-700 dark:text-navy-100">Sick Leave</h4>
                        <p>30 days per 36-month cycle, medical certificate required after 2 days</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-700 dark:text-navy-100">Family Leave</h4>
                        <p>3 days per year for family responsibilities</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-slate-700 dark:text-navy-100">Maternity Leave</h4>
                        <p>120 days (4 months) consecutive, medical certificate required</p>
                    </div>
                </div>
            </div>

            <!-- Current Balances -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-success/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Current Balances
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($leaveTypes as $leaveType)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600 dark:text-navy-200">{{ $leaveType->name }}</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                {{ $balances[$leaveType->id] ?? 0 }} days
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card w-full space-y-4 rounded-xl p-4 sm:px-5 mt-4">
                <div class="flex items-center justify-between space-x-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                Quick Actions
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('leave-requests.balances') }}"
                        class="btn w-full bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        View All Balances
                    </a>
                    <a href="{{ route('leave-requests.index') }}"
                        class="btn w-full bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View All Requests
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide medical certificate section based on leave type
        document.addEventListener('DOMContentLoaded', function() {
            const leaveTypeSelect = document.querySelector('select[name="leave_type_id"]');
            const medicalSection = document.getElementById('medical-certificate-section');
            
            function toggleMedicalCertificate() {
                const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
                const requiresMedical = selectedOption.getAttribute('data-requires-medical') === 'true';
                
                if (requiresMedical) {
                    medicalSection.style.display = 'block';
                } else {
                    medicalSection.style.display = 'none';
                }
            }
            
            leaveTypeSelect.addEventListener('change', toggleMedicalCertificate);
            toggleMedicalCertificate(); // Check on page load
        });
    </script>
@endsection