<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Host Locations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">QR Code Locations</h3>
                            <p class="text-sm text-gray-500">Manage check-in/out locations for learners</p>
                        </div>
                        <div>
                            <a href="{{ route('hosts.create') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Add Host Location
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hosts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($hosts as $host)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $host->name }}</h3>
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $host->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $host->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600">
                                <p><strong>Program:</strong> {{ $host->program->title }}</p>
                                <p><strong>Location:</strong> {{ $host->city }}, {{ $host->province }}</p>
                                <p><strong>Radius:</strong> {{ $host->radius_meters }}m</p>
                                <p><strong>QR Code:</strong> <code class="text-xs">{{ $host->qr_code }}</code></p>
                            </div>

                            <!-- QR Code Preview -->
                            <div class="mt-4 flex justify-center">
                                <div
                                    class="w-32 h-32 border border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                    <div class="text-center">
                                        <svg class="w-20 h-20 mx-auto text-gray-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 0v10h10V4H5z"
                                                clip-rule="evenodd" />
                                            <path d="M6 6h2v2H6V6zm4 0h2v2h-2V6zm-4 4h2v2H6v-2zm4 0h2v2h-2v-2z" />
                                        </svg>
                                        <p class="text-xs text-gray-500 mt-1">QR Code</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex justify-between">
                                <a href="{{ route('hosts.show', $host) }}"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm">View Details</a>
                                <a href="{{ route('hosts.edit', $host) }}"
                                    class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No host locations</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new host location.</p>
                            <div class="mt-6">
                                <a href="{{ route('hosts.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add Host Location
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($hosts->hasPages())
                <div class="mt-6">
                    {{ $hosts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>