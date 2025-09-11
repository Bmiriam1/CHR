<div class="sidebar print:hidden">
    <!-- Main Sidebar -->
    <div class="main-sidebar">
        <div
            class="flex h-full w-full flex-col items-center border-r border-slate-150 bg-white dark:border-navy-700 dark:bg-navy-800">
            <!-- Application Logo -->
            <div class="flex pt-4">
                <a href="/">
                    <img class="size-11 transition-transform duration-500 ease-in-out hover:rotate-[360deg]"
                        src="{{ asset('assets/images/icon.svg') }}" alt="logo" />
                </a>
            </div>

            <!-- Main Sections Links -->
            <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'Dashboard'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path fill="currentColor" fill-opacity=".3"
                            d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z" />
                        <path fill="currentColor"
                            d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z" />
                        <path fill="currentColor"
                            d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z" />
                        <path fill="currentColor"
                            d="M17.5 5h-1a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5Z" />
                    </svg>
                </a>

                @can('viewAny', \App\Models\Company::class)
                    <!-- Companies -->
                    <a href="{{ route('companies.index') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Companies'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19 7.001c0 3.865-3.134 7-7 7s-7-3.135-7-7c0-3.867 3.134-7.001 7-7.001s7 3.134 7 7.001z"
                                fill="currentColor" fill-opacity="0.3" />
                            <path
                                d="M12 14.001c2.206 0 4.438.896 6.072 2.53a.999.999 0 01.263 1.477L17 19.001H7l-1.335-1.993a.999.999 0 01.263-1.477c1.634-1.634 3.866-2.53 6.072-2.53z"
                                fill="currentColor" />
                            <path d="M7 19.001h10l1.5 2H5.5l1.5-2z" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                @endcan

                @can('view schedules')
                    <!-- Schedules -->
                    <a href="{{ route('schedules.index') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Training Schedules'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                                fill="currentColor" />
                        </svg>
                    </a>
                @endcan

                @can('view hosts')
                    <!-- Hosts -->
                    <a href="{{ route('hosts.index') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Host Locations'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill-opacity="0.3" />
                            <path d="M12 2L2 7l10 5 10-5-10-5z" fill="currentColor" />
                        </svg>
                    </a>
                @endcan

                @can('view payslips')
                    <!-- Payslips -->
                    <a href="{{ route('payslips.index') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Payslips'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.1 3.89 23 5 23H19C20.1 23 21 22.1 21 21V9M19 9H14V3.5L19 9Z"
                                fill="currentColor" />
                            <circle cx="8" cy="16" r="2" fill="currentColor" fill-opacity="0.3" />
                            <circle cx="16" cy="16" r="2" fill="currentColor" fill-opacity="0.3" />
                        </svg>
                    </a>
                @endcan

                <!-- Leave Management -->
                <a href="{{ route('leave-requests.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                    x-tooltip.placement.right="'Leave Management'">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 11h2v2H7zm0 4h2v2H7zm4-4h2v2h-2zm0 4h2v2h-2zm4-4h2v2h-2zm0 4h2v2h-2z" fill="currentColor" fill-opacity="0.3"/>
                        <path d="M5 3v4h14V3c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2z" fill="currentColor"/>
                        <path d="M19 5H5v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5zM7 11h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2zM7 15h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2z" fill="currentColor"/>
                    </svg>
                </a>

                @can('view compliance dashboard')
                    <!-- Compliance & Reports -->
                    <a href="{{ route('compliance.dashboard') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'SARS Compliance'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11H7v8h2v-8zm4-4h-2v12h2V7zm4-4h-2v16h2V3z" fill="currentColor"
                                fill-opacity="0.3" />
                            <path d="M5 14h14M5 14l1.5-1.5M5 14l1.5 1.5M19 14l-1.5-1.5M19 14l-1.5 1.5" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" />
                            <path d="M3 21h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                @endcan

                @can('view devices')
                    <!-- Devices -->
                    <a href="{{ route('devices.index') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Device Management'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 2v20l-2-1.5L13 22l-2-1.5L9 22l-2-1.5L5 22V2l2 1.5L9 2l2 1.5L13 2l2 1.5z"
                                fill="currentColor" fill-opacity="0.3" />
                            <path d="M9 7h6M9 11h6M9 15h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                @endcan
            </div>

            <!-- Bottom Links -->
            <div class="flex flex-col items-center space-y-3 py-3">
                @can('view profile')
                    <!-- Settings -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                        x-tooltip.placement.right="'Settings'">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-opacity="0.3" fill="currentColor"
                                d="M2 12.947v-1.771c0-1.047.85-1.913 1.899-1.913 1.81 0 2.549-1.288 1.64-2.868a1.919 1.919 0 0 1 .699-2.607l1.729-.996c.79-.474 1.81-.192 2.279.603l.11.192c.9 1.58 2.379 1.58 3.288 0l.11-.192c.47-.795 1.49-1.077 2.279-.603l1.73.996a1.92 1.92 0 0 1 .699 2.607c-.91 1.58-.17 2.868 1.639 2.868 1.04 0 1.899.856 1.899 1.912v1.772c0 1.047-.85 1.912-1.9 1.912-1.808 0-2.548 1.288-1.638 2.869.52.915.21 2.083-.7 2.606l-1.729.997c-.79.473-1.81.191-2.279-.604l-.11-.191c-.9-1.58-2.379-1.58-3.288 0l-.11.19c-.47.796-1.49 1.078-2.279.605l-1.73-.997a1.919 1.919 0 0 1-.699-2.606c.91-1.58.17-2.869-1.639-2.869A1.911 1.911 0 0 1 2 12.947Z" />
                            <path fill="currentColor"
                                d="M11.995 15.332c1.794 0 3.248-1.464 3.248-3.27 0-1.807-1.454-3.272-3.248-3.272-1.794 0-3.248 1.465-3.248 3.271 0 1.807 1.454 3.271 3.248 3.271Z" />
                        </svg>
                    </a>
                @endcan

                <!-- Profile -->
                <div x-data="usePopper({placement:'right-end',offset:12})"
                    @click.outside="isShowPopper && (isShowPopper = false)" class="flex">
                    <button @click="isShowPopper = !isShowPopper" x-ref="popperRef"
                        class="avatar size-12 cursor-pointer">
                        <img class="rounded-full" src="images/200x200.png" alt="avatar" />
                        <span
                            class="absolute right-0 size-3.5 rounded-full border-2 border-white bg-success dark:border-navy-700"></span>
                    </button>

                    <div :class="isShowPopper && 'show'" class="popper-root fixed" x-ref="popperRoot">
                        <div
                            class="popper-box w-64 rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-600 dark:bg-navy-700">
                            <div
                                class="flex items-center space-x-4 rounded-t-lg bg-slate-100 py-5 px-4 dark:bg-navy-800">
                                <div class="avatar size-14">
                                    <img class="rounded-full" src="images/200x200.png" alt="avatar" />
                                </div>
                                <div>
                                    <a href="{{ route('profile.edit') }}"
                                        class="text-base font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">
                                        {{ auth()->user()->first_name ?? 'User' }} {{ auth()->user()->last_name ?? '' }}
                                    </a>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">
                                        @if(auth()->user()->hasRole('learner'))
                                            {{ auth()->user()->occupation ?? 'Learner' }}
                                        @elseif(auth()->user()->hasRole('admin'))
                                            System Administrator
                                        @elseif(auth()->user()->hasRole('company_admin'))
                                            Company Administrator
                                        @elseif(auth()->user()->hasRole('hr_manager'))
                                            HR Manager
                                        @else
                                            {{ auth()->user()->occupation ?? 'Employee' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col pt-2 pb-5">
                                @can('view profile')
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                        <div
                                            class="flex size-8 items-center justify-center rounded-lg bg-warning text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                                Profile
                                            </h2>
                                            <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                                Your profile setting
                                            </div>
                                        </div>
                                    </a>
                                @endcan
                                @can('view payslips')
                                    <a href="{{ route('payslips.index') }}"
                                        class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                        <div class="flex size-8 items-center justify-center rounded-lg bg-info text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                                Payslips
                                            </h2>
                                            <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                                View and download payslips
                                            </div>
                                        </div>
                                    </a>
                                @endcan
                                @can('view attendance')
                                    <a href="{{ route('attendance.index') }}"
                                        class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                        <div
                                            class="flex size-8 items-center justify-center rounded-lg bg-secondary text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                                Attendance
                                            </h2>
                                            <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                                Track and view attendance
                                            </div>
                                        </div>
                                    </a>
                                @endcan
                                @can('view compliance dashboard')
                                    <a href="{{ route('compliance.dashboard') }}"
                                        class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                        <div class="flex size-8 items-center justify-center rounded-lg bg-error text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                                SARS Reports
                                            </h2>
                                            <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                                Compliance and tax reporting
                                            </div>
                                        </div>
                                    </a>
                                @endcan
                                @can('view profile')
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                        <div
                                            class="flex size-8 items-center justify-center rounded-lg bg-success text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                                Settings
                                            </h2>
                                            <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                                Account settings
                                            </div>
                                        </div>
                                    </a>
                                @endcan
                                <div class="mt-3 px-4">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn h-9 w-full space-x-2 bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Panel -->
    <div class="sidebar-panel">
        <div class="flex h-full grow flex-col bg-white pl-[var(--main-sidebar-width)] dark:bg-navy-750">
            <!-- Sidebar Panel Header -->
            <div class="flex h-18 w-full items-center justify-between pl-4 pr-1">
                <p class="text-base tracking-wider text-slate-800 dark:text-navy-100">
                    Connect HR
                </p>
                <button @click="$store.global.isSidebarExpanded = false"
                    class="btn h-7 w-7 rounded-full p-0 text-primary hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:text-accent-light/80 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 xl:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Panel Body -->
            <div x-data="{expandedItem:null}" class="h-[calc(100%-4.5rem)] overflow-x-hidden pb-6"
                x-init="$el._x_simplebar = new SimpleBar($el);">
                <ul class="flex flex-1 flex-col px-4 font-inter">
                    <li>
                        <a x-data="navLink" href="{{ route('dashboard') }}"
                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                            class="flex py-2 text-xs-plus tracking-wide outline-hidden transition-colors duration-300 ease-in-out">
                            @if(auth()->user()->hasRole('learner'))
                                Learner Dashboard
                            @else
                                Company Dashboard
                            @endif
                        </a>
                    </li>
                </ul>
                @can('view companies')
                    <div class="my-3 mx-4 h-px bg-slate-200 dark:bg-navy-500"></div>
                    <ul class="flex flex-1 flex-col px-4 font-inter">
                        <li x-data="accordionItem('clients-menu')">
                            <a :class="expanded ? 'text-slate-800 font-semibold dark:text-navy-50' : 'text-slate-600 dark:text-navy-200  hover:text-slate-800  dark:hover:text-navy-50'"
                                @click="expanded = !expanded"
                                class="flex items-center justify-between py-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out"
                                href="javascript:void(0);">
                                <span>Company Management</span>
                                <svg :class="expanded && 'rotate-90'" xmlns="http://www.w3.org/2000/svg"
                                    class="size-4 text-slate-400 transition-transform ease-in-out" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <ul x-collapse x-show="expanded">
                                <li>
                                    <a x-data="navLink" href="{{ route('companies.index') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>View Companies</span>
                                        </div>
                                    </a>
                                </li>
                                @can('create companies')
                                    <li>
                                        <a x-data="navLink" href="{{ route('companies.create') }}"
                                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                                <span>Add Company</span>
                                            </div>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                @endcan

                @can('view schedules')
                    <div class="my-3 mx-4 h-px bg-slate-200 dark:bg-navy-500"></div>
                    <ul class="flex flex-1 flex-col px-4 font-inter">
                        <li x-data="accordionItem('schedules-menu')">
                            <a :class="expanded ? 'text-slate-800 font-semibold dark:text-navy-50' : 'text-slate-600 dark:text-navy-200  hover:text-slate-800  dark:hover:text-navy-50'"
                                @click="expanded = !expanded"
                                class="flex items-center justify-between py-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out"
                                href="javascript:void(0);">
                                <span>Training Schedules</span>
                                <svg :class="expanded && 'rotate-90'" xmlns="http://www.w3.org/2000/svg"
                                    class="size-4 text-slate-400 transition-transform ease-in-out" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <ul x-collapse x-show="expanded">
                                <li>
                                    <a x-data="navLink" href="{{ route('schedules.index') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>View Schedules</span>
                                        </div>
                                    </a>
                                </li>
                                @can('create schedules')
                                    <li>
                                        <a x-data="navLink" href="{{ route('schedules.create') }}"
                                            :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                            class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                                <span>Create Schedule</span>
                                            </div>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                @endcan

                @can('view compliance dashboard')
                    <div class="my-3 mx-4 h-px bg-slate-200 dark:bg-navy-500"></div>
                    <ul class="flex flex-1 flex-col px-4 font-inter">
                        <li x-data="accordionItem('compliance-menu')">
                            <a :class="expanded ? 'text-slate-800 font-semibold dark:text-navy-50' : 'text-slate-600 dark:text-navy-200  hover:text-slate-800  dark:hover:text-navy-50'"
                                @click="expanded = !expanded"
                                class="flex items-center justify-between py-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out"
                                href="javascript:void(0);">
                                <span>SARS Compliance</span>
                                <svg :class="expanded && 'rotate-90'" xmlns="http://www.w3.org/2000/svg"
                                    class="size-4 text-slate-400 transition-transform ease-in-out" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <ul x-collapse x-show="expanded">
                                <li>
                                    <a x-data="navLink" href="{{ route('compliance.dashboard') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>Compliance Dashboard</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a x-data="navLink" href="{{ route('compliance.sars.emp201.form') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>EMP201 Returns</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a x-data="navLink" href="{{ route('compliance.eti.dashboard') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>ETI Claims</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a x-data="navLink" href="{{ route('compliance.tax_certificates.index') }}"
                                        :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'"
                                        class="flex items-center justify-between p-2 text-xs-plus tracking-wide outline-hidden transition-[color,padding-left] duration-300 ease-in-out hover:pl-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="size-1.5 rounded-full border border-current opacity-40"></div>
                                            <span>Tax Certificates</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endcan
            </div>
        </div>
    </div>
</div>