@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Create New Program
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Add a new training program to the system
                    </p>
                </div>
                <div>
                    <a href="{{ route('programs.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Programs
                    </a>
                </div>
            </div>

            <form action="{{ route('programs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="card mt-6">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Basic Information
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Program Title *</span>
                                <input type="text" name="title" value="{{ old('title') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="Enter program title" required>
                                @error('title')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Program Code</span>
                                <input type="text" name="program_code" value="{{ old('program_code') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="Auto-generated if empty">
                                @error('program_code')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Program Type *</span>
                                <select name="program_type_id"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>
                                    <option value="">Select Program Type</option>
                                    @foreach($programTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('program_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_type_id')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Coordinator</span>
                                <select name="coordinator_id"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    <option value="">Select Coordinator</option>
                                    @foreach($coordinators as $coordinator)
                                        <option value="{{ $coordinator->id }}" {{ old('coordinator_id') == $coordinator->id ? 'selected' : '' }}>
                                            {{ $coordinator->first_name }} {{ $coordinator->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('coordinator_id')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="sm:col-span-2">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Description</span>
                                    <textarea name="description" rows="3"
                                        class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        placeholder="Program description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-xs+ text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Dates & Financial -->
                <div class="card mt-6">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Dates & Financial Details
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Start Date *</span>
                                <input type="date" name="start_date" value="{{ old('start_date') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>
                                @error('start_date')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">End Date *</span>
                                <input type="date" name="end_date" value="{{ old('end_date') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" required>
                                @error('end_date')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Daily Rate *</span>
                                <input type="number" step="0.01" min="0" name="daily_rate" value="{{ old('daily_rate') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="0.00" required>
                                @error('daily_rate')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Transport Allowance</span>
                                <input type="number" step="0.01" min="0" name="transport_allowance" value="{{ old('transport_allowance', '0.00') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="0.00">
                                @error('transport_allowance')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Payment Frequency</span>
                                <select name="payment_frequency"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    <option value="monthly" {{ old('payment_frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="weekly" {{ old('payment_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="daily" {{ old('payment_frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                </select>
                                @error('payment_frequency')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Payment Day of Month</span>
                                <input type="number" min="1" max="31" name="payment_day_of_month" value="{{ old('payment_day_of_month', '25') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('payment_day_of_month')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Location & Logistics -->
                <div class="card mt-6">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Location & Logistics
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Location Type</span>
                                <select name="location_type"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    <option value="onsite" {{ old('location_type', 'onsite') == 'onsite' ? 'selected' : '' }}>On-site</option>
                                    <option value="online" {{ old('location_type') == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="hybrid" {{ old('location_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                                @error('location_type')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Max Learners</span>
                                <input type="number" min="1" name="max_learners" value="{{ old('max_learners', '25') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('max_learners')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Venue</span>
                                <input type="text" name="venue" value="{{ old('venue') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="Training venue name">
                                @error('venue')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Min Learners</span>
                                <input type="number" min="1" name="min_learners" value="{{ old('min_learners', '5') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('min_learners')
                                    <span class="text-xs+ text-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="sm:col-span-2">
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Venue Address</span>
                                    <textarea name="venue_address" rows="2"
                                        class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        placeholder="Full venue address...">{{ old('venue_address') }}</textarea>
                                    @error('venue_address')
                                        <span class="text-xs+ text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance & Settings -->
                <div class="card mt-6">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Compliance & Settings
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Section 12H -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="section_12h_eligible" id="section_12h_eligible" value="1" {{ old('section_12h_eligible') ? 'checked' : '' }}
                                        class="form-checkbox size-4 rounded border-slate-400/70 bg-transparent before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent">
                                    <label for="section_12h_eligible" class="text-sm font-medium text-slate-600 dark:text-navy-100">Section 12H Eligible</label>
                                </div>

                                <label class="block">
                                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Section 12H Contract Number</span>
                                    <input type="text" name="section_12h_contract_number" value="{{ old('section_12h_contract_number') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        placeholder="Contract number">
                                    @error('section_12h_contract_number')
                                        <span class="text-xs+ text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>

                            <!-- ETI -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="eti_eligible_program" id="eti_eligible_program" value="1" {{ old('eti_eligible_program') ? 'checked' : '' }}
                                        class="form-checkbox size-4 rounded border-slate-400/70 bg-transparent before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent">
                                    <label for="eti_eligible_program" class="text-sm font-medium text-slate-600 dark:text-navy-100">ETI Eligible</label>
                                </div>

                                <label class="block">
                                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">ETI Category</span>
                                    <select name="eti_category"
                                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                        <option value="">Select Category</option>
                                        <option value="youth" {{ old('eti_category') == 'youth' ? 'selected' : '' }}>Youth</option>
                                        <option value="disabled" {{ old('eti_category') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                        <option value="other" {{ old('eti_category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('eti_category')
                                        <span class="text-xs+ text-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card mt-6">
                    <div class="px-4 py-4 sm:px-5">
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('programs.index') }}"
                                class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                Cancel
                            </a>
                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Create Program
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection