@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Companies
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Manage your companies and sub-companies
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('clients.create') }}"
                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent-focus/90">
                        <i class="fa fa-plus mr-2"></i>
                        Add Company
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

            @if($primaryClients->count() > 0)
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6 xl:grid-cols-4">
                    @foreach($primaryClients as $client)
                        <div class="card grow items-center p-4 sm:p-5">
                            <div class="avatar size-20">
                                <img class="rounded-full" src="images/200x200.png" alt="avatar">
                                <div
                                    class="absolute right-0 m-1 size-4 rounded-full border-2 border-white {{ $client->status === 'active' ? 'bg-primary dark:border-navy-700 dark:bg-accent' : 'bg-slate-300 dark:border-navy-700' }}">
                                </div>
                            </div>
                            <h3 class="pt-3 text-lg font-medium text-slate-700 dark:text-navy-100">
                                {{ $client->name }}
                            </h3>
                            <p class="text-xs-plus">{{ $client->code }}</p>
                            <div class="my-4 h-px w-full bg-slate-200 dark:bg-navy-500"></div>
                            <div class="grow space-y-4">
                                @if($client->contact_person)
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="flex h-7 w-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa fa-user text-xs"></i>
                                        </div>
                                        <p>{{ $client->contact_person }}</p>
                                    </div>
                                @endif
                                @if($client->email)
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="flex h-7 w-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa fa-envelope text-xs"></i>
                                        </div>
                                        <p>{{ $client->email }}</p>
                                    </div>
                                @endif
                                @if($client->phone)
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="flex h-7 w-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa fa-phone text-xs"></i>
                                        </div>
                                        <p>{{ $client->phone }}</p>
                                    </div>
                                @endif
                                @if($client->city)
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="flex h-7 w-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa fa-map-marker text-xs"></i>
                                        </div>
                                        <p>{{ $client->city }}, {{ $client->province }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-5 flex space-x-2">
                                <a href="{{ route('clients.show', $client) }}"
                                    class="btn space-x-2 rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>View</span>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}"
                                    class="btn space-x-2 rounded-full bg-slate-150 font-medium text-slate-700 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-100 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                        </path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 text-center py-12">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-navy-600">
                        <i class="fa fa-building text-2xl text-slate-400 dark:text-navy-300"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-slate-700 dark:text-navy-100">No companies</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-navy-300">Get started by creating a new company.</p>
                    <div class="mt-6">
                        <a href="{{ route('clients.create') }}"
                            class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent-focus/90">
                            <i class="fa fa-plus mr-2"></i>
                            Add Company
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection