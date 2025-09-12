@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        @if($parentCompany)
                            Add Sub-Client
                        @else
                            Add Client
                        @endif
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        @if($parentCompany)
                            Create a new sub-client under {{ $parentCompany->name }}
                        @else
                            Create a new client
                        @endif
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('companies.index') }}" 
                       class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Create Form -->
            <div class="card mt-6">
                <form action="{{ route('companies.store') }}" method="POST">
                    @csrf
                
                    <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-navy-500">
                        <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Client Information</h3>
                        <p class="text-sm text-slate-500 dark:text-navy-300 mt-1">Basic details about the client</p>
                    </div>
                    
                    <div class="p-4 sm:p-5 space-y-5">
                        <!-- Company Names -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label for="name" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                    Company Name <span class="text-error">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('name') !border-error @enderror"
                                       placeholder="Enter company name"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="display_name" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                    Display Name
                                </label>
                                <input type="text" 
                                       name="display_name" 
                                       id="display_name" 
                                       value="{{ old('display_name') }}"
                                       class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('display_name') !border-error @enderror"
                                       placeholder="Display name (optional)">
                                @error('display_name')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="trading_name" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                Trading Name
                            </label>
                            <input type="text" 
                                   name="trading_name" 
                                   id="trading_name" 
                                   value="{{ old('trading_name') }}"
                                   class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('trading_name') !border-error @enderror"
                                   placeholder="Trading name (if different)">
                            @error('trading_name')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label for="company_registration_number" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                    Registration Number
                                </label>
                                <input type="text" 
                                       name="company_registration_number" 
                                       id="company_registration_number" 
                                       value="{{ old('company_registration_number') }}"
                                       class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent font-mono @error('company_registration_number') !border-error @enderror"
                                       placeholder="e.g. 2021/123456/07">
                                @error('company_registration_number')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="vat_number" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                    VAT Number
                                </label>
                                <input type="text" 
                                       name="vat_number" 
                                       id="vat_number" 
                                       value="{{ old('vat_number') }}"
                                       class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent font-mono @error('vat_number') !border-error @enderror"
                                       placeholder="e.g. 4123456789">
                                @error('vat_number')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="border-t border-slate-200 dark:border-navy-500 pt-5">
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Contact Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label for="email" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('email') !border-error @enderror"
                                           placeholder="contact@company.com">
                                    @error('email')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('phone') !border-error @enderror"
                                           placeholder="+27 11 123 4567">
                                    @error('phone')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="border-t border-slate-200 dark:border-navy-500 pt-5">
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Physical Address</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label for="physical_address_line1" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        Address Line 1
                                    </label>
                                    <input type="text" 
                                           name="physical_address_line1" 
                                           id="physical_address_line1" 
                                           value="{{ old('physical_address_line1') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('physical_address_line1') !border-error @enderror"
                                           placeholder="Street address">
                                    @error('physical_address_line1')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="physical_address_line2" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        Address Line 2
                                    </label>
                                    <input type="text" 
                                           name="physical_address_line2" 
                                           id="physical_address_line2" 
                                           value="{{ old('physical_address_line2') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('physical_address_line2') !border-error @enderror"
                                           placeholder="Suburb, building, etc. (optional)">
                                    @error('physical_address_line2')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mt-4">
                                <div>
                                    <label for="physical_city" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        City
                                    </label>
                                    <input type="text" 
                                           name="physical_city" 
                                           id="physical_city" 
                                           value="{{ old('physical_city') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('physical_city') !border-error @enderror"
                                           placeholder="City">
                                    @error('physical_city')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="physical_postal_code" class="block text-xs+ font-medium text-slate-700 dark:text-navy-100 mb-1">
                                        Postal Code
                                    </label>
                                    <input type="text" 
                                           name="physical_postal_code" 
                                           id="physical_postal_code" 
                                           value="{{ old('physical_postal_code') }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent @error('physical_postal_code') !border-error @enderror"
                                           placeholder="Postal code">
                                    @error('physical_postal_code')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-slate-200 dark:border-navy-500 pt-5">
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="form-checkbox size-4 rounded border-slate-300 bg-transparent hover:border-slate-400 focus:border-primary checked:bg-primary checked:border-primary focus:ring-primary/25 dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent dark:checked:bg-accent dark:checked:border-accent dark:focus:ring-accent/25">
                                <span class="text-xs+ font-medium text-slate-700 dark:text-navy-100">Active</span>
                            </label>
                            <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">
                                Uncheck to create the client as inactive
                            </p>
                        </div>

                        @if($parentCompany)
                            <input type="hidden" name="parent_company_id" value="{{ $parentCompany->id }}">
                        @endif
                        
                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-navy-500">
                            <a href="{{ route('companies.index') }}" 
                               class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                @if($parentCompany)
                                    Create Sub-Client
                                @else
                                    Create Client
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
