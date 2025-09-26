@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    @include('components.sidebar')

    <div class="flex-1">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ __('Profile Settings') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Manage your account information, documents, and preferences') }}
                        </p>
                    </div>

                    <!-- Back Button -->
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition duration-200 shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-8">
                
                <!-- Profile Completion Progress -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ __('Profile Completion') }}</h3>
                            <p class="text-blue-100 text-sm">{{ __('Complete your profile to unlock all features') }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $completedFields = 0;
                                $totalFields = 8;
                                
                                if($user->name || ($user->first_name && $user->last_name)) $completedFields++;
                                if($user->email) $completedFields++;
                                if($user->bank_name) $completedFields++;
                                if($user->qualification_document) $completedFields++;
                                if($user->cv_document) $completedFields++;
                                if($user->banking_statement) $completedFields++;
                                if($user->id_document) $completedFields++;
                                if($user->phone_number || $user->phone) $completedFields++;
                                
                                $completionPercentage = round(($completedFields / $totalFields) * 100);
                            @endphp
                            <div class="text-2xl font-bold">{{ $completionPercentage }}%</div>
                            <div class="text-blue-100 text-xs">{{ __('Complete') }}</div>
                        </div>
                    </div>
                    <div class="w-full bg-blue-400 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Left Column -->
                    <div class="space-y-8">
                        
                        <!-- Basic Profile Information -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <header class="mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ __('Profile Information') }}
                                        </h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Update your basic account information') }}
                                        </p>
                                    </div>
                                </div>
                            </header>
                            @include('profile.partials.update-profile-information-form')
                        </div>

                        <!-- Password Update -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <header class="mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-orange-100 dark:bg-orange-900/30 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ __('Security') }}
                                        </h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Update your password to keep your account secure') }}
                                        </p>
                                    </div>
                                </div>
                            </header>
                            @include('profile.partials.update-password-form')
                        </div>

                        <!-- Additional Learner Information -->
                        @include('profile.partials.learner-info-form')
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8">
                        
                        <!-- Banking Details -->
                        @include('profile.partials.banking-details-form')

                        <!-- Document Upload -->
                        @include('profile.partials.document-upload-form')
                    </div>
                </div>

                <!-- Full Width Sections -->
                <div class="space-y-8">
                    
                    <!-- Account Verification Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <header class="mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="bg-cyan-100 dark:bg-cyan-900/30 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('Account Verification Status') }}
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Track your account verification progress') }}
                                    </p>
                                </div>
                            </div>
                        </header>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Email Verification -->
                            <div class="text-center p-4 rounded-lg {{ $user->email_verified_at ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $user->email_verified_at ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-200 dark:bg-gray-600' }}">
                                    <svg class="w-6 h-6 {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium {{ $user->email_verified_at ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">{{ __('Email') }}</h4>
                                <p class="text-xs {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                    {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                </p>
                            </div>

                            <!-- Banking Verification -->
                            <div class="text-center p-4 rounded-lg {{ $user->banking_verified ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $user->banking_verified ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-200 dark:bg-gray-600' }}">
                                    <svg class="w-6 h-6 {{ $user->banking_verified ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium {{ $user->banking_verified ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">{{ __('Banking') }}</h4>
                                <p class="text-xs {{ $user->banking_verified ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                    {{ $user->banking_verified ? __('Verified') : __('Pending') }}
                                </p>
                            </div>

                            <!-- Documents Verification -->
                            <div class="text-center p-4 rounded-lg {{ $user->allDocumentsVerified() ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $user->allDocumentsVerified() ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-200 dark:bg-gray-600' }}">
                                    <svg class="w-6 h-6 {{ $user->allDocumentsVerified() ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium {{ $user->allDocumentsVerified() ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">{{ __('Documents') }}</h4>
                                <p class="text-xs {{ $user->allDocumentsVerified() ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                    {{ $user->allDocumentsVerified() ? __('Verified') : __('Pending') }}
                                </p>
                            </div>

                            <!-- Overall Status -->
                            <div class="text-center p-4 rounded-lg {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? 'bg-green-50 dark:bg-green-900/20' : 'bg-gray-50 dark:bg-gray-700' }}">
                                <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-200 dark:bg-gray-600' }}">
                                    <svg class="w-6 h-6 {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-medium {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">{{ __('Overall') }}</h4>
                                <p class="text-xs {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                    {{ $user->email_verified_at && $user->banking_verified && $user->allDocumentsVerified() ? __('Complete') : __('In Progress') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 p-6">
                        <header class="mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="bg-red-100 dark:bg-red-900/40 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-red-900 dark:text-red-100">
                                        {{ __('Danger Zone') }}
                                    </h2>
                                    <p class="text-sm text-red-700 dark:text-red-300">
                                        {{ __('Irreversible actions that will permanently delete your data') }}
                                    </p>
                                </div>
                            </div>
                        </header>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection