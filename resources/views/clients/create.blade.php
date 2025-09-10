@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        {{ $parentClient ? 'Add Sub-company to ' . $parentClient->name : 'Add New Company' }}
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        {{ $parentClient ? 'Create a new sub-company under ' . $parentClient->name : 'Create a new company for your organization' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('clients.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Back to Companies
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="mt-6 alert bg-error/10 text-error dark:bg-error/15">
                    <div class="flex items-center space-x-2">
                        <i class="fa fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="mt-6 card">
                <div class="p-4">
                    <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
                        @csrf

                        @if($parentClient)
                            <input type="hidden" name="parent_client_id" value="{{ $parentClient->id }}">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Basic Information</h3>

                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Company Name
                                        *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('name')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Company Code
                                        *</label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('code')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if(!$parentClient && $parentClients->count() > 0)
                                    <div>
                                        <label for="parent_client_id"
                                            class="block text-sm font-medium text-slate-700 dark:text-navy-100">Parent
                                            Client</label>
                                        <select name="parent_client_id" id="parent_client_id"
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                            <option value="">Select a parent client (optional)</option>
                                            @foreach($parentClients as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_client_id') == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name }} ({{ $parent->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_client_id')
                                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Contact Information</h3>

                                <div>
                                    <label for="contact_person"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Contact
                                        Person</label>
                                    <input type="text" name="contact_person" id="contact_person"
                                        value="{{ old('contact_person') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('contact_person')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('email')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Phone</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Address Information</h3>

                            <div>
                                <label for="address"
                                    class="block text-sm font-medium text-slate-700 dark:text-navy-100">Address</label>
                                <textarea name="address" id="address" rows="2"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="city"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">City</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('city')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="province"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Province</label>
                                    <input type="text" name="province" id="province" value="{{ old('province') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('province')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="postal_code"
                                        class="block text-sm font-medium text-slate-700 dark:text-navy-100">Postal
                                        Code</label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                    @error('postal_code')
                                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="country"
                                    class="block text-sm font-medium text-slate-700 dark:text-navy-100">Country</label>
                                <input type="text" name="country" id="country" value="{{ old('country', 'South Africa') }}"
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                @error('country')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('clients.index') }}"
                                class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                Cancel
                            </a>
                            <button type="submit"
                                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent-focus/90">
                                {{ $parentClient ? 'Create Sub-client' : 'Create Client' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection