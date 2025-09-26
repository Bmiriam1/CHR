<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <header class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Banking Details') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Secure banking information for verification and payments') }}
                </p>
            </div>
        </div>
    </header>

    <form method="POST" action="{{ route('profile.initiate-bank-verification') }}" class="space-y-6">
        @csrf
        @method('post')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Bank Name') }} <span class="text-red-500">*</span>
                </label>
                <select id="bank_name" name="bank_name" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200">
                    <option value="">{{ __('Select Your Bank') }}</option>
                    <option value="ABSA Bank" {{ old('bank_name', $user->bank_name) == 'ABSA Bank' ? 'selected' : '' }}>ABSA Bank</option>
                    <option value="Standard Bank" {{ old('bank_name', $user->bank_name) == 'Standard Bank' ? 'selected' : '' }}>Standard Bank</option>
                    <option value="First National Bank" {{ old('bank_name', $user->bank_name) == 'First National Bank' ? 'selected' : '' }}>First National Bank (FNB)</option>
                    <option value="Nedbank" {{ old('bank_name', $user->bank_name) == 'Nedbank' ? 'selected' : '' }}>Nedbank</option>
                    <option value="Capitec Bank" {{ old('bank_name', $user->bank_name) == 'Capitec Bank' ? 'selected' : '' }}>Capitec Bank</option>
                    <option value="African Bank" {{ old('bank_name', $user->bank_name) == 'African Bank' ? 'selected' : '' }}>African Bank</option>
                    <option value="Discovery Bank" {{ old('bank_name', $user->bank_name) == 'Discovery Bank' ? 'selected' : '' }}>Discovery Bank</option>
                    <option value="TymeBank" {{ old('bank_name', $user->bank_name) == 'TymeBank' ? 'selected' : '' }}>TymeBank</option>
                    <option value="Other" {{ old('bank_name', $user->bank_name) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @if($errors->bankingDetails->has('bank_name'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->bankingDetails->first('bank_name') }}</span>
                    </p>
                @endif
            </div>
            
            <div class="space-y-2">
                <label for="account_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Account Type') }} <span class="text-red-500">*</span>
                </label>
                <select id="account_type" name="account_type" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200">
                    <option value="">{{ __('Select Account Type') }}</option>
                    <option value="savings" {{ old('account_type', $user->account_type) == 'savings' ? 'selected' : '' }}>{{ __('Savings Account') }}</option>
                    <option value="current" {{ old('account_type', $user->account_type) == 'current' ? 'selected' : '' }}>{{ __('Current Account') }}</option>
                    <option value="cheque" {{ old('account_type', $user->account_type) == 'cheque' ? 'selected' : '' }}>{{ __('Cheque Account') }}</option>
                </select>
                @if($errors->bankingDetails->has('account_type'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->bankingDetails->first('account_type') }}</span>
                    </p>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Account Number') }} <span class="text-red-500">*</span>
                </label>
                <input id="bank_account_number" name="bank_account_number" type="text" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200" 
                    value="{{ old('bank_account_number', $user->bank_account_number) }}" 
                    placeholder="Enter your account number">
                @if($errors->bankingDetails->has('bank_account_number'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->bankingDetails->first('bank_account_number') }}</span>
                    </p>
                @endif
            </div>
            
            <div class="space-y-2">
                <label for="bank_branch_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Branch Code') }} <span class="text-gray-500 text-xs">(Optional)</span>
                </label>
                <input id="bank_branch_code" name="bank_branch_code" type="text" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200" 
                    value="{{ old('bank_branch_code', $user->bank_branch_code) }}" 
                    placeholder="e.g., 632005">
                @if($errors->bankingDetails->has('bank_branch_code'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->bankingDetails->first('bank_branch_code') }}</span>
                    </p>
                @endif
            </div>
        </div>
        
        <div class="space-y-2">
            <label for="account_holder_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ __('Account Holder Name') }} <span class="text-red-500">*</span>
            </label>
            <input id="account_holder_name" name="account_holder_name" type="text" 
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200" 
                value="{{ old('account_holder_name', $user->account_holder_name) }}" 
                placeholder="Full name as it appears on bank account">
            @if($errors->bankingDetails->has('account_holder_name'))
                <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $errors->bankingDetails->first('account_holder_name') }}</span>
                </p>
            @endif
        </div>

        <!-- Verification Status -->
        @if($user->banking_verified)
            <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">
                        {{ __('Banking Details Verified') }}
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400">
                        {{ __('Your banking information has been successfully verified.') }}
                    </p>
                </div>
            </div>
        @elseif($user->bank_name)
            <div class="flex items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-300">
                        {{ __('Verification Pending') }}
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400">
                        {{ __('Please upload a recent banking statement to complete verification.') }}
                    </p>
                </div>
            </div>
        @endif
        
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="submit" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 !text-white font-medium rounded-lg shadow-sm transition duration-200 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save Banking Details') }}
            </button>
            
            @if (session('status') === 'banking-updated')
                <div class="flex items-center text-green-600 dark:text-green-400 animate-fade-in">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ __('Banking details saved successfully!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>