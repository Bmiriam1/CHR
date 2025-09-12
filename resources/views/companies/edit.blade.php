@extends('layouts.app')

@section('content')
    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Edit {{ $company->name }}
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        @if($company->parent_company_id)
                            Sub-client of {{ $company->parentCompany->name ?? 'N/A' }}
                        @else
                            Primary Client
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('companies.show', $company) }}"
                       class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Company
                    </a>
                    @if($company->parent_company_id)
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this client? This action cannot be undone.')" 
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                                <i class="fa fa-trash mr-2"></i>
                                Delete Client
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
        
        <!-- Edit Form -->
        <div class="col-span-12">
                <form action="{{ route('companies.update', $company) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Client Information
                            </h2>
                            <div class="flex items-center space-x-2">
                                <i class="fa fa-building text-warning"></i>
                                <span class="text-sm text-slate-500 dark:text-navy-300">Edit Details</span>
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
                                           value="{{ old('name', $company->name) }}"
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
                                           value="{{ old('trading_name', $company->trading_name) }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('trading_name') border-error @enderror"
                                           placeholder="Trading name (if different)">
                                    @error('trading_name')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Registration Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="registration_number" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Registration Number
                                    </label>
                                    <input type="text" 
                                           name="registration_number" 
                                           id="registration_number" 
                                           value="{{ old('registration_number', $company->registration_number) }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('registration_number') border-error @enderror font-mono"
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
                                           value="{{ old('vat_number', $company->vat_number) }}"
                                           class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('vat_number') border-error @enderror font-mono"
                                           placeholder="4123456789">
                                    @error('vat_number')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="border-t border-slate-200 dark:border-navy-500 pt-6">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Contact Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                            Email Address
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email', $company->email) }}"
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
                                               value="{{ old('phone', $company->phone) }}"
                                               class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('phone') border-error @enderror"
                                               placeholder="+27 11 234 5678">
                                        @error('phone')
                                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="border-t border-slate-200 dark:border-navy-500 pt-6">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Physical Address</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="physical_address_line1" class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                            Street Address
                                        </label>
                                        <input type="text" 
                                               name="physical_address_line1" 
                                               id="physical_address_line1" 
                                               value="{{ old('physical_address_line1', $company->physical_address_line1) }}"
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
                                                   value="{{ old('physical_city', $company->physical_city) }}"
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
                                                <option value="Western Cape" {{ old('physical_province', $company->physical_province) == 'Western Cape' ? 'selected' : '' }}>Western Cape</option>
                                                <option value="Eastern Cape" {{ old('physical_province', $company->physical_province) == 'Eastern Cape' ? 'selected' : '' }}>Eastern Cape</option>
                                                <option value="Northern Cape" {{ old('physical_province', $company->physical_province) == 'Northern Cape' ? 'selected' : '' }}>Northern Cape</option>
                                                <option value="Free State" {{ old('physical_province', $company->physical_province) == 'Free State' ? 'selected' : '' }}>Free State</option>
                                                <option value="KwaZulu-Natal" {{ old('physical_province', $company->physical_province) == 'KwaZulu-Natal' ? 'selected' : '' }}>KwaZulu-Natal</option>
                                                <option value="North West" {{ old('physical_province', $company->physical_province) == 'North West' ? 'selected' : '' }}>North West</option>
                                                <option value="Gauteng" {{ old('physical_province', $company->physical_province) == 'Gauteng' ? 'selected' : '' }}>Gauteng</option>
                                                <option value="Mpumalanga" {{ old('physical_province', $company->physical_province) == 'Mpumalanga' ? 'selected' : '' }}>Mpumalanga</option>
                                                <option value="Limpopo" {{ old('physical_province', $company->physical_province) == 'Limpopo' ? 'selected' : '' }}>Limpopo</option>
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
                                                   value="{{ old('physical_postal_code', $company->physical_postal_code) }}"
                                                   class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-primary focus:bg-white dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:placeholder:text-navy-400 dark:focus:border-accent @error('physical_postal_code') border-error @enderror"
                                                   placeholder="2196">
                                            @error('physical_postal_code')
                                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="border-t border-slate-200 dark:border-navy-500 pt-6">
                                <div class="flex items-center">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active" 
                                           value="1"
                                           {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                                           class="form-checkbox size-4 rounded border-slate-300 bg-white checked:bg-primary checked:border-primary focus:ring-primary/25 focus:ring-offset-0 dark:border-navy-450 dark:bg-navy-700 dark:checked:bg-accent dark:checked:border-accent dark:focus:ring-accent/25">
                                    <label for="is_active" class="ml-3 block text-sm text-slate-700 dark:text-navy-100">
                                        Active (client can create programs and manage learners)
                                    </label>
                                </div>
                                <p class="mt-2 text-sm text-slate-400 dark:text-navy-300">
                                    Inactive clients cannot create new programs or enroll learners.
                                </p>
                            </div>

                            <!-- System Information -->
                            @if($company->created_at || $company->updated_at)
                            <div class="border-t border-slate-200 dark:border-navy-500 pt-6">
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">System Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-slate-400 dark:text-navy-300">Created</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                            {{ $company->created_at ? $company->created_at->format('M j, Y g:i A') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-400 dark:text-navy-300">Last Updated</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                            {{ $company->updated_at ? $company->updated_at->format('M j, Y g:i A') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-navy-500">
                                <div class="text-sm text-slate-500 dark:text-navy-300">
                                    @if($company->parent_company_id)
                                        Sub-client of <strong>{{ $company->parentCompany->name ?? 'N/A' }}</strong>
                                    @else
                                        Primary client
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('companies.show', $company) }}"
                                       class="btn border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                            class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                                        <i class="fa fa-save mr-2"></i>
                                        Update Client
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
    </div>
@endsection