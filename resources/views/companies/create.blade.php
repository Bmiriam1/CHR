@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Add Sub-Client
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Create a new sub-client under {{ $parentCompany->name }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('companies.index') }}"
                       class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Companies
                    </a>
                </div>
            </div>

        </div>
        
        <!-- Create Form -->
        <div class="col-span-12">
                <form action="{{ route('companies.store') }}" method="POST">
                    @csrf
                    
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Client Information
                            </h2>
                            <div class="flex items-center space-x-2">
                                <i class="fa fa-building text-primary"></i>
                                <span class="text-sm text-slate-500 dark:text-navy-300">Sub-Client</span>
                            </div>
                        </div>
                        
                        <div class="px-4 pb-4 sm:px-5 space-y-6">
                            <!-- Company Names -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Company Name <span class="text-error">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('name') border-error @enderror"
                                           placeholder="Enter company name"
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="trading_name" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Trading Name
                                    </label>
                                    <input type="text" 
                                           name="trading_name" 
                                           id="trading_name" 
                                           value="{{ old('trading_name') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('trading_name') border-error @enderror"
                                           placeholder="Trading name (if different)">
                                    @error('trading_name')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('email') border-error @enderror"
                                           placeholder="client@example.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('phone') border-error @enderror"
                                           placeholder="+27 11 234 5678">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Registration Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="registration_number" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Registration Number
                                    </label>
                                    <input type="text" 
                                           name="registration_number" 
                                           id="registration_number" 
                                           value="{{ old('registration_number') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('registration_number') border-error @enderror"
                                           placeholder="2019/123456/07">
                                    @error('registration_number')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="vat_number" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        VAT Number
                                    </label>
                                    <input type="text" 
                                           name="vat_number" 
                                           id="vat_number" 
                                           value="{{ old('vat_number') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('vat_number') border-error @enderror"
                                           placeholder="4123456789">
                                    @error('vat_number')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div>
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Address Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="physical_address_line1" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                            Street Address
                                        </label>
                                        <input type="text" 
                                               name="physical_address_line1" 
                                               id="physical_address_line1" 
                                               value="{{ old('physical_address_line1') }}"
                                               class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('physical_address_line1') border-error @enderror"
                                               placeholder="123 Business Street">
                                        @error('physical_address_line1')
                                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="physical_city" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                                City
                                            </label>
                                            <input type="text" 
                                                   name="physical_city" 
                                                   id="physical_city" 
                                                   value="{{ old('physical_city') }}"
                                                   class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('physical_city') border-error @enderror"
                                                   placeholder="Johannesburg">
                                            @error('physical_city')
                                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="physical_province" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                                Province
                                            </label>
                                            <select name="physical_province" 
                                                    id="physical_province"
                                                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:focus:border-accent @error('physical_province') border-error @enderror">
                                                <option value="">Select Province</option>
                                                <option value="Western Cape" {{ old('physical_province') == 'Western Cape' ? 'selected' : '' }}>Western Cape</option>
                                                <option value="Eastern Cape" {{ old('physical_province') == 'Eastern Cape' ? 'selected' : '' }}>Eastern Cape</option>
                                                <option value="Northern Cape" {{ old('physical_province') == 'Northern Cape' ? 'selected' : '' }}>Northern Cape</option>
                                                <option value="Free State" {{ old('physical_province') == 'Free State' ? 'selected' : '' }}>Free State</option>
                                                <option value="KwaZulu-Natal" {{ old('physical_province') == 'KwaZulu-Natal' ? 'selected' : '' }}>KwaZulu-Natal</option>
                                                <option value="North West" {{ old('physical_province') == 'North West' ? 'selected' : '' }}>North West</option>
                                                <option value="Gauteng" {{ old('physical_province') == 'Gauteng' ? 'selected' : '' }}>Gauteng</option>
                                                <option value="Mpumalanga" {{ old('physical_province') == 'Mpumalanga' ? 'selected' : '' }}>Mpumalanga</option>
                                                <option value="Limpopo" {{ old('physical_province') == 'Limpopo' ? 'selected' : '' }}>Limpopo</option>
                                            </select>
                                            @error('physical_province')
                                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <label for="physical_postal_code" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                                Postal Code
                                            </label>
                                            <input type="text" 
                                                   name="physical_postal_code" 
                                                   id="physical_postal_code" 
                                                   value="{{ old('physical_postal_code') }}"
                                                   class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('physical_postal_code') border-error @enderror"
                                                   placeholder="2196">
                                            @error('physical_postal_code')
                                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden parent company ID -->
                            <input type="hidden" name="parent_company_id" value="{{ $parentCompany->id }}">

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-navy-500">
                                <a href="{{ route('companies.index') }}"
                                   class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                    <i class="fa fa-plus mr-2"></i>
                                    Create Sub-Client
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
    </div>
@endsection