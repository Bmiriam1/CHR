<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <header class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="bg-indigo-100 dark:bg-indigo-900/30 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Additional Information') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Complete your learner profile with additional details') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update-learner-info') }}" class="space-y-6">
        @csrf
        @method('patch')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Phone Number') }}
                </label>
                <input id="phone_number" name="phone_number" type="tel" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                    value="{{ old('phone_number', $user->phone_number ?? $user->phone) }}" 
                    placeholder="+27 XX XXX XXXX">
                @if($errors->learnerInfo->has('phone_number'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->learnerInfo->first('phone_number') }}</span>
                    </p>
                @endif
            </div>
            
            <div class="space-y-2">
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Date of Birth') }}
                </label>
                <input id="date_of_birth" name="date_of_birth" type="date" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                    value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : ($user->birth_date ? $user->birth_date->format('Y-m-d') : '')) }}">
                @if($errors->learnerInfo->has('date_of_birth'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->learnerInfo->first('date_of_birth') }}</span>
                    </p>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <label for="physical_address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ __('Physical Address') }}
            </label>
            <textarea id="physical_address" name="physical_address" rows="3"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                placeholder="Enter your full physical address">{{ old('physical_address', $user->physical_address) }}</textarea>
            @if($errors->learnerInfo->has('physical_address'))
                <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $errors->learnerInfo->first('physical_address') }}</span>
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Emergency Contact Name') }}
                </label>
                <input id="emergency_contact_name" name="emergency_contact_name" type="text" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                    value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" 
                    placeholder="Full name of emergency contact">
                @if($errors->learnerInfo->has('emergency_contact_name'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->learnerInfo->first('emergency_contact_name') }}</span>
                    </p>
                @endif
            </div>
            
            <div class="space-y-2">
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('Emergency Contact Phone') }}
                </label>
                <input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                    value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" 
                    placeholder="+27 XX XXX XXXX">
                @if($errors->learnerInfo->has('emergency_contact_phone'))
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $errors->learnerInfo->first('emergency_contact_phone') }}</span>
                    </p>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <label for="education_level" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ __('Highest Education Level') }}
            </label>
            <select id="education_level" name="education_level" 
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                <option value="">{{ __('Select Education Level') }}</option>
                <option value="matric" {{ old('education_level', $user->education_level) == 'matric' ? 'selected' : '' }}>{{ __('Matric / Grade 12') }}</option>
                <option value="diploma" {{ old('education_level', $user->education_level) == 'diploma' ? 'selected' : '' }}>{{ __('Diploma') }}</option>
                <option value="degree" {{ old('education_level', $user->education_level) == 'degree' ? 'selected' : '' }}>{{ __('Bachelor\'s Degree') }}</option>
                <option value="postgraduate" {{ old('education_level', $user->education_level) == 'postgraduate' ? 'selected' : '' }}>{{ __('Postgraduate') }}</option>
                <option value="other" {{ old('education_level', $user->education_level) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
            </select>
            @if($errors->learnerInfo->has('education_level'))
                <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $errors->learnerInfo->first('education_level') }}</span>
                </p>
            @endif
        </div>
        
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
            <button type="submit" 
                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 !text-white font-medium rounded-lg shadow-sm transition duration-200 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save Additional Information') }}
            </button>
            
            @if (session('status') === 'learner-info-updated')
                <div class="flex items-center text-green-600 dark:text-green-400 animate-fade-in">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ __('Information updated successfully!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>