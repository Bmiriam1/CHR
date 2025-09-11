<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit {{ $company->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    @if($company->isBranch())
                        Sub-client of {{ $company->parentCompany->name }}
                    @else
                        Primary Client
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('companies.show', $company) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Cancel
                </a>
                @if($company->isBranch())
                    <form action="{{ route('companies.destroy', $company) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this client? This action cannot be undone.')" 
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Delete Client
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('companies.update', $company) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Client Information</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Update client details and settings</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Company Names -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $company->name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('name') border-red-500 @enderror"
                                       placeholder="Enter company name"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Display Name
                                </label>
                                <input type="text" 
                                       name="display_name" 
                                       id="display_name" 
                                       value="{{ old('display_name', $company->display_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('display_name') border-red-500 @enderror"
                                       placeholder="Display name (optional)">
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="trading_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Trading Name
                            </label>
                            <input type="text" 
                                   name="trading_name" 
                                   id="trading_name" 
                                   value="{{ old('trading_name', $company->trading_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('trading_name') border-red-500 @enderror"
                                   placeholder="Trading name (if different)">
                            @error('trading_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Registration Number
                                </label>
                                <input type="text" 
                                       name="company_registration_number" 
                                       id="company_registration_number" 
                                       value="{{ old('company_registration_number', $company->company_registration_number) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 font-mono @error('company_registration_number') border-red-500 @enderror"
                                       placeholder="e.g. 2021/123456/07">
                                @error('company_registration_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="vat_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    VAT Number
                                </label>
                                <input type="text" 
                                       name="vat_number" 
                                       id="vat_number" 
                                       value="{{ old('vat_number', $company->vat_number) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 font-mono @error('vat_number') border-red-500 @enderror"
                                       placeholder="e.g. 4123456789">
                                @error('vat_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Contact Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $company->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('email') border-red-500 @enderror"
                                           placeholder="contact@company.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone" 
                                           value="{{ old('phone', $company->phone) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('phone') border-red-500 @enderror"
                                           placeholder="+27 11 123 4567">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Physical Address</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="physical_address_line1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Address Line 1
                                    </label>
                                    <input type="text" 
                                           name="physical_address_line1" 
                                           id="physical_address_line1" 
                                           value="{{ old('physical_address_line1', $company->physical_address_line1) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('physical_address_line1') border-red-500 @enderror"
                                           placeholder="Street address">
                                    @error('physical_address_line1')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="physical_address_line2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Address Line 2
                                    </label>
                                    <input type="text" 
                                           name="physical_address_line2" 
                                           id="physical_address_line2" 
                                           value="{{ old('physical_address_line2', $company->physical_address_line2) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('physical_address_line2') border-red-500 @enderror"
                                           placeholder="Suite, unit, building, floor, etc.">
                                    @error('physical_address_line2')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="physical_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            City
                                        </label>
                                        <input type="text" 
                                               name="physical_city" 
                                               id="physical_city" 
                                               value="{{ old('physical_city', $company->physical_city) }}"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('physical_city') border-red-500 @enderror"
                                               placeholder="City">
                                        @error('physical_city')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="physical_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Postal Code
                                        </label>
                                        <input type="text" 
                                               name="physical_postal_code" 
                                               id="physical_postal_code" 
                                               value="{{ old('physical_postal_code', $company->physical_postal_code) }}"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 @error('physical_postal_code') border-red-500 @enderror"
                                               placeholder="Postal Code">
                                        @error('physical_postal_code')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                    Active (client can create programs and manage learners)
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Inactive clients cannot create new programs or enroll learners.
                            </p>
                        </div>

                        <!-- System Information (Read-only) -->
                        @if($company->created_at || $company->updated_at)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">System Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $company->created_at ? $company->created_at->format('M j, Y g:i A') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $company->updated_at ? $company->updated_at->format('M j, Y g:i A') : 'N/A' }}</dd>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @if($company->isBranch())
                                Sub-client of <strong>{{ $company->parentCompany->name }}</strong>
                            @else
                                Primary client
                            @endif
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('companies.show', $company) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Update Client
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>