@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Host Location</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new host location for attendance tracking</p>
            </div>
            <a href="{{ route('hosts.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Back to Hosts
            </a>
        </div>

        <form action="{{ route('hosts.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Host Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="program_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Program <span
                                class="text-red-500">*</span></label>
                        <select name="program_id" id="program_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->title }} ({{ $program->program_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Location Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address_search"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="address_search" placeholder="Start typing an address..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search for an address to auto-populate
                            location details</p>
                    </div>

                    <div>
                        <label for="address_line1"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Line 1 <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('address_line1')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address_line2"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Line 2</label>
                        <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('address_line2')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="province"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="province" id="province" value="{{ old('province') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('province')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="country" id="country" value="{{ old('country', 'South Africa') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="latitude"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Latitude <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="latitude" id="latitude" step="any" value="{{ old('latitude') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Longitude <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="longitude" id="longitude" step="any" value="{{ old('longitude') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="radius_meters"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Radius (meters)
                            <span class="text-red-500">*</span></label>
                        <input type="number" name="radius_meters" id="radius_meters" min="10" max="10000"
                            value="{{ old('radius_meters', 100) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('radius_meters')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_person"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="contact_email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Check-in Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Check-in Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="check_in_start_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in Start
                            Time</label>
                        <input type="time" name="check_in_start_time" id="check_in_start_time"
                            value="{{ old('check_in_start_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('check_in_start_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="check_in_end_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-in End
                            Time</label>
                        <input type="time" name="check_in_end_time" id="check_in_end_time"
                            value="{{ old('check_in_end_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('check_in_end_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="check_out_start_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-out Start
                            Time</label>
                        <input type="time" name="check_out_start_time" id="check_out_start_time"
                            value="{{ old('check_out_start_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('check_out_start_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="check_out_end_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Check-out End
                            Time</label>
                        <input type="time" name="check_out_end_time" id="check_out_end_time"
                            value="{{ old('check_out_end_time') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        @error('check_out_end_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_daily_check_ins"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Daily Check-ins
                            <span class="text-red-500">*</span></label>
                        <input type="number" name="max_daily_check_ins" id="max_daily_check_ins" min="1" max="10"
                            value="{{ old('max_daily_check_ins', 1) }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        @error('max_daily_check_ins')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="allow_multiple_check_ins" id="allow_multiple_check_ins"
                                    value="1" {{ old('allow_multiple_check_ins') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="allow_multiple_check_ins"
                                    class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Allow Multiple Check-ins
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="require_supervisor_approval" id="require_supervisor_approval"
                                    value="1" {{ old('require_supervisor_approval') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="require_supervisor_approval"
                                    class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Require Supervisor Approval
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('hosts.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    Create Host Location
                </button>
            </div>
        </form>
    </div>

    <!-- Google Places API -->
    <script>
        let autocomplete;
        let place;

        function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById('address_search'),
                {
                    types: ['establishment', 'geocode'],
                    componentRestrictions: { country: 'za' } // Restrict to South Africa
                }
            );

            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            place = autocomplete.getPlace();

            if (!place.geometry || !place.geometry.location) {
                console.log('No details available for input: ' + place.name);
                return;
            }

            // Fill in the address components
            const addressComponents = place.address_components;
            let addressLine1 = '';
            let city = '';
            let province = '';
            let postalCode = '';
            let country = 'South Africa';

            // Extract address components
            addressComponents.forEach(component => {
                const types = component.types;

                if (types.includes('street_number') || types.includes('route')) {
                    if (addressLine1) addressLine1 += ' ';
                    addressLine1 += component.long_name;
                }

                if (types.includes('locality') || types.includes('administrative_area_level_2')) {
                    city = component.long_name;
                }

                if (types.includes('administrative_area_level_1')) {
                    province = component.long_name;
                }

                if (types.includes('postal_code')) {
                    postalCode = component.long_name;
                }

                if (types.includes('country')) {
                    country = component.long_name;
                }
            });

            // If no specific address line 1, use the formatted address
            if (!addressLine1) {
                addressLine1 = place.formatted_address.split(',')[0];
            }

            // Fill the form fields
            document.getElementById('address_line1').value = addressLine1;
            document.getElementById('city').value = city;
            document.getElementById('province').value = province;
            document.getElementById('postal_code').value = postalCode;
            document.getElementById('country').value = country;
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            // Load Google Maps API
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API_KEY") }}&libraries=places&callback=initAutocomplete';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        });
    </script>
@endsection
