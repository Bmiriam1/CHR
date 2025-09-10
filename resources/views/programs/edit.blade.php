@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Program</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Update program details</p>
        </div>
        <a href="{{ route('programs.show', $program) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
            Back to Program
        </a>
    </div>

    <form action="{{ route('programs.update', $program) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $program->title) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="program_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program Code <span class="text-red-500">*</span></label>
                    <input type="text" name="program_code" id="program_code" value="{{ old('program_code', $program->program_code) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('program_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $program->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="coordinator_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Coordinator</label>
                    <select name="coordinator_id" id="coordinator_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Coordinator</option>
                        @foreach($coordinators as $coordinator)
                            <option value="{{ $coordinator->id }}" {{ old('coordinator_id', $program->coordinator_id) == $coordinator->id ? 'selected' : '' }}>
                                {{ $coordinator->first_name }} {{ $coordinator->last_name }} ({{ $coordinator->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('coordinator_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Qualification Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Qualification Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nqf_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NQF Level <span class="text-red-500">*</span></label>
                    <select name="nqf_level" id="nqf_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select NQF Level</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('nqf_level', $program->nqf_level) == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                        @endfor
                    </select>
                    @error('nqf_level')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="saqa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SAQA ID</label>
                    <input type="text" name="saqa_id" id="saqa_id" value="{{ old('saqa_id', $program->saqa_id) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('saqa_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="qualification_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Qualification Title</label>
                    <input type="text" name="qualification_title" id="qualification_title" value="{{ old('qualification_title', $program->qualification_title) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('qualification_title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Duration and Scheduling -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Duration and Scheduling</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="duration_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_months" id="duration_months" min="1" value="{{ old('duration_months', $program->duration_months) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('duration_months')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_weeks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (Weeks)</label>
                    <input type="number" name="duration_weeks" id="duration_weeks" min="1" value="{{ old('duration_weeks', $program->duration_weeks) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('duration_weeks')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_training_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Training Days</label>
                    <input type="number" name="total_training_days" id="total_training_days" min="1" value="{{ old('total_training_days', $program->total_training_days) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('total_training_days')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $program->start_date) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $program->end_date) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="enrollment_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enrollment Start Date</label>
                    <input type="date" name="enrollment_start_date" id="enrollment_start_date" value="{{ old('enrollment_start_date', $program->enrollment_start_date) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('enrollment_start_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="enrollment_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enrollment End Date</label>
                    <input type="date" name="enrollment_end_date" id="enrollment_end_date" value="{{ old('enrollment_end_date', $program->enrollment_end_date) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('enrollment_end_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Financial Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Financial Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="daily_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Daily Rate (R) <span class="text-red-500">*</span></label>
                    <input type="number" name="daily_rate" id="daily_rate" step="0.01" min="0" value="{{ old('daily_rate', $program->daily_rate) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('daily_rate')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="monthly_stipend" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Stipend (R)</label>
                    <input type="number" name="monthly_stipend" id="monthly_stipend" step="0.01" min="0" value="{{ old('monthly_stipend', $program->monthly_stipend) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('monthly_stipend')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Frequency <span class="text-red-500">*</span></label>
                    <select name="payment_frequency" id="payment_frequency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select Frequency</option>
                        <option value="daily" {{ old('payment_frequency', $program->payment_frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('payment_frequency', $program->payment_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('payment_frequency', $program->payment_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    @error('payment_frequency')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transport_allowance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transport Allowance (R)</label>
                    <input type="number" name="transport_allowance" id="transport_allowance" step="0.01" min="0" value="{{ old('transport_allowance', $program->transport_allowance) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('transport_allowance')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meal_allowance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meal Allowance (R)</label>
                    <input type="number" name="meal_allowance" id="meal_allowance" step="0.01" min="0" value="{{ old('meal_allowance', $program->meal_allowance) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('meal_allowance')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="accommodation_allowance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Accommodation Allowance (R)</label>
                    <input type="number" name="accommodation_allowance" id="accommodation_allowance" step="0.01" min="0" value="{{ old('accommodation_allowance', $program->accommodation_allowance) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('accommodation_allowance')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Enrollment and Location -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Enrollment and Location</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="max_learners" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Learners <span class="text-red-500">*</span></label>
                    <input type="number" name="max_learners" id="max_learners" min="1" value="{{ old('max_learners', $program->max_learners) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('max_learners')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="min_learners" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Learners</label>
                    <input type="number" name="min_learners" id="min_learners" min="1" value="{{ old('min_learners', $program->min_learners) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('min_learners')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Type <span class="text-red-500">*</span></label>
                    <select name="location_type" id="location_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select Location Type</option>
                        <option value="onsite" {{ old('location_type', $program->location_type) == 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="offsite" {{ old('location_type', $program->location_type) == 'offsite' ? 'selected' : '' }}>Offsite</option>
                        <option value="online" {{ old('location_type', $program->location_type) == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="hybrid" {{ old('location_type', $program->location_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    @error('location_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="venue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Venue</label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', $program->venue) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('venue')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="venue_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Venue Address</label>
                    <textarea name="venue_address" id="venue_address" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('venue_address', $program->venue_address) }}</textarea>
                    @error('venue_address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- South African Compliance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">South African Compliance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ETI Section -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="eti_eligible_program" id="eti_eligible_program" value="1" {{ old('eti_eligible_program', $program->eti_eligible_program) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="eti_eligible_program" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            ETI Eligible Program
                        </label>
                    </div>
                    
                    <div>
                        <label for="eti_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ETI Category</label>
                        <input type="text" name="eti_category" id="eti_category" value="{{ old('eti_category', $program->eti_category) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('eti_category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section 12H -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="section_12h_eligible" id="section_12h_eligible" value="1" {{ old('section_12h_eligible', $program->section_12h_eligible) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="section_12h_eligible" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Section 12H Eligible
                        </label>
                    </div>
                    
                    <div>
                        <label for="section_12h_allowance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Section 12H Allowance (R)</label>
                        <input type="number" name="section_12h_allowance" id="section_12h_allowance" step="0.01" min="0" value="{{ old('section_12h_allowance', $program->section_12h_allowance) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('section_12h_allowance')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="bbbee_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">B-BBEE Category</label>
                    <input type="text" name="bbbee_category" id="bbbee_category" value="{{ old('bbbee_category', $program->bbbee_category) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('bbbee_category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="specific_requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specific Requirements</label>
                    <textarea name="specific_requirements" id="specific_requirements" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('specific_requirements', $program->specific_requirements) }}</textarea>
                    @error('specific_requirements')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('programs.show', $program) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Update Program
            </button>
        </div>
    </form>
</div>
@endsection