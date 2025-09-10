@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        {{ $client->name }}
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">{{ $client->code }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('clients.create', ['parent_client_id' => $client->id]) }}"
                        class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Add Sub-company
                    </a>
                    <a href="{{ route('programs.create', ['client_id' => $client->id]) }}"
                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <i class="fa fa-graduation-cap mr-2"></i>
                        Add Program
                    </a>
                    <a href="{{ route('clients.edit', $client) }}"
                        class="btn bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                        <i class="fa fa-edit mr-2"></i>
                        Edit Client
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this client? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                            <i class="fa fa-trash mr-2"></i>
                            Delete Client
                        </button>
                    </form>
                </div>
            </div>

            <!-- Client Information -->
            <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2">
                <div class="card">
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4">Client Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Name</dt>
                                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $client->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Code</dt>
                                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $client->code }}</dd>
                            </div>
                            @if($client->description)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Description</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $client->description }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Status</dt>
                                <dd>
                                    <span
                                        class="badge 
                                            {{ $client->status === 'active' ? 'bg-success/10 text-success dark:bg-success/15' :
        ($client->status === 'inactive' ? 'bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-100' : 'bg-error/10 text-error dark:bg-error/15') }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4">Contact Information</h3>
                        <dl class="space-y-3">
                            @if($client->contact_person)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Contact Person</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $client->contact_person }}</dd>
                                </div>
                            @endif
                            @if($client->email)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Email</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">
                                        <a href="mailto:{{ $client->email }}" class="text-primary hover:text-primary-focus">
                                            {{ $client->email }}
                                        </a>
                                    </dd>
                                </div>
                            @endif
                            @if($client->phone)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Phone</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">
                                        <a href="tel:{{ $client->phone }}" class="text-primary hover:text-primary-focus">
                                            {{ $client->phone }}
                                        </a>
                                    </dd>
                                </div>
                            @endif
                            @if($client->address)
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 dark:text-navy-300">Address</dt>
                                    <dd class="text-sm text-slate-700 dark:text-navy-100">
                                        {{ $client->address }}<br>
                                        @if($client->city)
                                            {{ $client->city }}, {{ $client->province }} {{ $client->postal_code }}<br>
                                        @endif
                                        {{ $client->country }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sub-companies Section -->
            @if($client->subClients->count() > 0)
                <div class="mt-6 card">
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Sub-companies
                                ({{ $client->subClients->count() }})</h3>
                            <a href="{{ route('clients.create', ['parent_client_id' => $client->id]) }}"
                                class="text-primary hover:text-primary-focus text-sm font-medium">
                                Add Sub-company
                            </a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($client->subClients as $subClient)
                                <div
                                    class="border border-slate-200 dark:border-navy-500 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-navy-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-slate-700 dark:text-navy-100">{{ $subClient->name }}</h4>
                                        <span
                                            class="badge 
                                                        {{ $subClient->status === 'active' ? 'bg-success/10 text-success dark:bg-success/15' : 'bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-100' }}">
                                            {{ ucfirst($subClient->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-navy-200 mb-2">{{ $subClient->code }}</p>
                                    @if($subClient->contact_person)
                                        <p class="text-xs text-slate-500 dark:text-navy-300">{{ $subClient->contact_person }}</p>
                                    @endif
                                    <div class="mt-3 flex justify-between">
                                        <a href="{{ route('clients.show', $subClient) }}"
                                            class="text-primary hover:text-primary-focus text-sm">
                                            View
                                        </a>
                                        <a href="{{ route('clients.edit', $subClient) }}"
                                            class="text-warning hover:text-warning-focus text-sm">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Programs Section -->
            <div class="mt-6 card">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">Programs
                            ({{ $allPrograms->count() }})</h3>
                        <a href="{{ route('programs.create', ['client_id' => $client->id]) }}"
                            class="text-primary hover:text-primary-focus text-sm font-medium">
                            Add Program
                        </a>
                    </div>

                    @if($allPrograms->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">Program</th>
                                        <th class="whitespace-nowrap">Type</th>
                                        <th class="whitespace-nowrap">Duration</th>
                                        <th class="whitespace-nowrap">Status</th>
                                        <th class="whitespace-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPrograms as $program)
                                                        <tr>
                                                            <td class="whitespace-nowrap">
                                                                <div>
                                                                    <div class="font-medium text-slate-700 dark:text-navy-100">{{ $program->title }}
                                                                    </div>
                                                                    <div class="text-sm text-slate-500 dark:text-navy-300">
                                                                        {{ $program->program_code }}</div>
                                                                </div>
                                                            </td>
                                                            <td class="whitespace-nowrap text-sm text-slate-700 dark:text-navy-100">
                                                                {{ $program->programType->name ?? 'N/A' }}
                                                            </td>
                                                            <td class="whitespace-nowrap text-sm text-slate-700 dark:text-navy-100">
                                                                {{ $program->duration_months ? $program->duration_months . ' months' :
                                        ($program->duration_weeks ? $program->duration_weeks . ' weeks' : 'N/A') }}
                                                            </td>
                                                            <td class="whitespace-nowrap">
                                                                <span
                                                                    class="badge 
                                                                                {{ $program->status === 'active' ? 'bg-success/10 text-success dark:bg-success/15' :
                                        ($program->status === 'inactive' ? 'bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-100' : 'bg-error/10 text-error dark:bg-error/15') }}">
                                                                    {{ ucfirst($program->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="whitespace-nowrap text-sm font-medium">
                                                                <a href="{{ route('programs.show', $program) }}"
                                                                    class="text-primary hover:text-primary-focus mr-3">View</a>
                                                                <a href="{{ route('programs.edit', $program) }}"
                                                                    class="text-warning hover:text-warning-focus">Edit</a>
                                                            </td>
                                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-navy-600">
                                <i class="fa fa-graduation-cap text-2xl text-slate-400 dark:text-navy-300"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-slate-700 dark:text-navy-100">No programs</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-navy-300">Get started by creating a new program for
                                this client.</p>
                            <div class="mt-6">
                                <a href="{{ route('programs.create', ['client_id' => $client->id]) }}"
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent-focus/90">
                                    <i class="fa fa-graduation-cap mr-2"></i>
                                    Add Program
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection