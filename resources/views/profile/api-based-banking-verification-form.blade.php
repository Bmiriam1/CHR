<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <header class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Bank Account Verification') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Secure automated verification through our banking partner API') }}
                </p>
            </div>
        </div>
    </header>

    <!-- Current Verification Status -->
    @if($user->banking_verification_status)
        <div class="mb-6">
            @if($user->banking_verification_status === 'verified')
                <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">
                            {{ __('Bank Account Verified') }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400">
                            {{ __('Verified on') }} {{ $user->banking_verified_at?->format('d M Y, H:i') }}
                        </p>
                        @if($user->bank_account_number)
                            <p class="text-xs text-green-600 dark:text-green-400">
                                {{ __('Account') }}: ****{{ substr($user->bank_account_number, -4) }}
                            </p>
                        @endif
                    </div>
                </div>

            @elseif($user->banking_verification_status === 'pending')
                <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 mr-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">
                            {{ __('Verification In Progress') }}
                        </p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400">
                            {{ __('Your bank account verification is being processed. You will be notified once complete.') }}
                        </p>
                        @if($user->banking_verification_reference)
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">
                                {{ __('Reference') }}: {{ $user->banking_verification_reference }}
                            </p>
                        @endif
                    </div>
                </div>

            @elseif($user->banking_verification_status === 'failed')
                <div class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">
                            {{ __('Verification Failed') }}
                        </p>
                        <p class="text-xs text-red-600 dark:text-red-400">
                            {{ __('Unable to verify bank account. Please check your details and try again.') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Verification Form -->
    @if(!$user->banking_verification_status || $user->banking_verification_status === 'failed')
        <form method="post" action="{{ route('profile.initiate-bank-verification') }}" class="space-y-6">
            @csrf
            
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('Secure Verification Process') }}</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                            {{ __('We use bank-grade security to verify that you are the legitimate account holder. This process typically takes 1-3 business days.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('Bank Account Number') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="bank_account_number" name="bank_account_number" type="text" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200" 
                        value="{{ old('bank_account_number', $user->bank_account_number) }}" 
                        placeholder="Enter your bank account number">
                    @if($errors->bankingVerification->has('bank_account_number'))
                        <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $errors->bankingVerification->first('bank_account_number') }}</span>
                        </p>
                    @endif
                </div>
                
                <div class="space-y-2">
                    <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('ID Number') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="id_number" name="id_number" type="text" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200" 
                        value="{{ old('id_number', $user->id_number) }}" 
                        placeholder="Enter your South African ID number"
                        maxlength="13">
                    @if($errors->bankingVerification->has('id_number'))
                        <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $errors->bankingVerification->first('id_number') }}</span>
                        </p>
                    @endif
                </div>
            </div>

            @if($errors->bankingVerification->has('banking_api'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-sm text-red-700 dark:text-red-300 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $errors->bankingVerification->first('banking_api') }}
                    </p>
                </div>
            @endif
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                @if($user->banking_verification_status === 'failed')
                    <button type="submit" formaction="{{ route('profile.retry-bank-verification') }}"
                        class="inline-flex items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg shadow-sm border border-orange-600 transition duration-200 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('Retry Verification') }}
                    </button>
                @else
                    <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm border border-blue-600 transition duration-200 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ __('Start Verification') }}
                    </button>
                @endif
                
                @if (session('status') === 'banking-verification-initiated')
                    <div class="flex items-center text-green-600 dark:text-green-400 animate-fade-in">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('message') }}</span>
                    </div>
                @endif
            </div>
        </form>
    @endif

    <!-- Already Verified - Show Account Info -->
    @if($user->banking_verification_status === 'verified')
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Verified Account Details') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if($user->account_holder_name)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Account Holder') }}:</span>
                        <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $user->account_holder_name }}</span>
                    </div>
                @endif
                @if($user->bank_name)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Bank') }}:</span>
                        <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $user->bank_name }}</span>
                    </div>
                @endif
                @if($user->bank_account_number)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Account') }}:</span>
                        <span class="text-gray-900 dark:text-gray-100 ml-2">****{{ substr($user->bank_account_number, -4) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>