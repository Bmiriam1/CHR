@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Request Leave
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Submit a leave request for your program
                    </p>
                </div>
                <a href="{{ url()->previous() }}"
                    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>

            <!-- Leave Request Form -->
            <div class="card">
                <div class="px-4 py-4 sm:px-5">
                    <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Program Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                Program <span class="text-error">*</span>
                            </label>
                            <select name="program_id" required
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">Select a program</option>
                                @foreach(auth()->user()->programs ?? [] as $program)
                                    <option value="{{ $program->id }}">{{ $program->title }}</option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Leave Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                Leave Type <span class="text-error">*</span>
                            </label>
                            <select name="leave_type" required
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                <option value="">Select leave type</option>
                                <option value="sick">Sick Leave</option>
                                <option value="personal">Personal Leave</option>
                                <option value="emergency">Emergency Leave</option>
                                <option value="other">Other</option>
                            </select>
                            @error('leave_type')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Start Date <span class="text-error">*</span>
                                </label>
                                <input type="date" name="start_date" required
                                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('start_date')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    End Date <span class="text-error">*</span>
                                </label>
                                <input type="date" name="end_date" required
                                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('end_date')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                Reason <span class="text-error">*</span>
                            </label>
                            <textarea name="reason" rows="4" required
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                placeholder="Please provide a detailed reason for your leave request..."></textarea>
                            @error('reason')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                Additional Notes
                            </label>
                            <textarea name="notes" rows="3"
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                placeholder="Any additional information..."></textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_emergency" value="1"
                                    class="form-checkbox is-basic size-4 rounded border-slate-400/70 bg-slate-100 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:bg-navy-100 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent">
                                <span class="ml-2 text-sm text-slate-700 dark:text-navy-100">
                                    This is an emergency leave request
                                </span>
                            </label>
                        </div>

                        <!-- Attachment -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                Supporting Documents (Optional)
                            </label>
                            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                            <p class="mt-1 text-xs text-slate-500 dark:text-navy-300">
                                Upload supporting documents (PDF, images, or documents)
                            </p>
                            @error('attachment')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ url()->previous() }}"
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
    </div>

    <script>
        // Auto-calculate duration when dates change
        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');

            function calculateDuration() {
                if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    // You could display this somewhere if needed
                    console.log('Leave duration:', diffDays, 'days');
                }
            }

            startDateInput.addEventListener('change', calculateDuration);
            endDateInput.addEventListener('change', calculateDuration);
        });
    </script>
@endsection
