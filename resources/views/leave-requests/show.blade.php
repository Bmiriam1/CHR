@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Leave Application - {{ $user->first_name }} {{ $user->last_name }}
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        Program: {{ $program->title }} | Year: {{ $leaveBalance->leave_year }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('programs.show', $program) }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Program
                    </a>
                    <a href="{{ route('leave-requests.create') }}"
                        class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Request
                    </a>
                </div>
            </div>

            <!-- Leave Balance Overview -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-4 mb-6">
                <!-- Total Entitled -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Entitled</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $leaveBalance->total_entitled }} days
                            </h3>
                            <p class="text-xs text-info">Annual allocation</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-info/10">
                            <i class="fa fa-calendar-check text-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Taken -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Total Taken</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $leaveBalance->total_taken }} days
                            </h3>
                            <p class="text-xs text-warning">{{ $leaveBalance->getUtilizationPercentage() }}% utilized</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-warning/10">
                            <i class="fa fa-calendar-times text-warning"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Balance -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Available Balance</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $leaveBalance->total_balance }} days
                            </h3>
                            <p class="text-xs {{ $leaveBalance->getBalanceStatusColor() }}">
                                {{ ucfirst($leaveBalance->getBalanceStatus()) }} status
                            </p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-success/10">
                            <i class="fa fa-calendar-plus text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Accrued Leave -->
                <div class="card">
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-xs+ text-slate-400 dark:text-navy-300">Accrued This Year</p>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $accruedLeave['accrued_days'] }} days
                            </h3>
                            <p class="text-xs text-primary">{{ $accruedLeave['months_elapsed'] }} months elapsed</p>
                        </div>
                        <div class="mask is-squircle flex size-10 items-center justify-center bg-primary/10">
                            <i class="fa fa-chart-line text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Balance Breakdown -->
            <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2 mb-6">
                <!-- Leave Types Breakdown -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Leave Types Breakdown
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <div class="space-y-4">
                            <!-- Sick Leave -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                                        <i class="fa fa-thermometer-half text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">Sick Leave</p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            {{ $leaveBalance->sick_leave_taken }}/{{ $leaveBalance->sick_leave_entitled }}
                                            days
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-700 dark:text-navy-100">
                                        {{ $leaveBalance->sick_leave_balance }} days
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-navy-300">remaining</p>
                                </div>
                            </div>

                            <!-- Personal Leave -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40">
                                        <i class="fa fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">Personal Leave</p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            {{ $leaveBalance->personal_leave_taken }}/{{ $leaveBalance->personal_leave_entitled }}
                                            days
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-700 dark:text-navy-100">
                                        {{ $leaveBalance->personal_leave_balance }} days
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-navy-300">remaining</p>
                                </div>
                            </div>

                            <!-- Emergency Leave -->
                            <div
                                class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/40">
                                        <i class="fa fa-exclamation-triangle text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">Emergency Leave</p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            {{ $leaveBalance->emergency_leave_taken }}/{{ $leaveBalance->emergency_leave_entitled }}
                                            days
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-700 dark:text-navy-100">
                                        {{ $leaveBalance->emergency_leave_balance }} days
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-navy-300">remaining</p>
                                </div>
                            </div>

                            <!-- Other Leave -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900/20">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-900/40">
                                        <i class="fa fa-ellipsis-h text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-navy-100">Other Leave</p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            {{ $leaveBalance->other_leave_taken }}/{{ $leaveBalance->other_leave_entitled }}
                                            days
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-700 dark:text-navy-100">
                                        {{ $leaveBalance->other_leave_balance }} days
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-navy-300">remaining</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Utilization Chart -->
                <div class="card">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                        <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                            Leave Utilization
                        </h2>
                    </div>
                    <div class="px-4 pb-4 sm:px-5">
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Overall
                                    Utilization</span>
                                <span
                                    class="text-sm text-slate-500 dark:text-navy-300">{{ $leaveBalance->getUtilizationPercentage() }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 dark:bg-navy-600">
                                <div class="h-2 rounded-full {{ $leaveBalance->getBalanceStatus() === 'critical' ? 'bg-red-500' : ($leaveBalance->getBalanceStatus() === 'warning' ? 'bg-yellow-500' : ($leaveBalance->getBalanceStatus() === 'moderate' ? 'bg-blue-500' : 'bg-green-500')) }}"
                                    style="width: {{ $leaveBalance->getUtilizationPercentage() }}%"></div>
                            </div>
                        </div>

                        <!-- Utilization Stats -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Days Entitled</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_entitled }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Days Taken</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_taken }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Days Remaining</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->total_balance }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-600 dark:text-navy-300">Accrual Rate</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-navy-100">{{ $leaveBalance->accrual_rate_per_month }}/month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave History -->
            <div class="card">
                <div class="flex items-center justify-between px-4 py-4 sm:px-5">
                    <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Leave History - {{ $leaveBalance->leave_year }}
                    </h2>
                    <div class="flex space-x-2">
                        <select class="form-select text-xs" onchange="filterLeaveHistory(this.value)">
                            <option value="all">All Types</option>
                            <option value="sick">Sick Leave</option>
                            <option value="personal">Personal Leave</option>
                            <option value="emergency">Emergency Leave</option>
                            <option value="other">Other Leave</option>
                        </select>
                        <select class="form-select text-xs" onchange="filterLeaveStatus(this.value)">
                            <option value="all">All Status</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="px-4 pb-4 sm:px-5">
                    @if($leaveRequests && $leaveRequests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left" id="leave-history-table">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-navy-500">
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Leave Type
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Dates
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Duration
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Reason
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-3 font-semibold uppercase tracking-wide text-slate-800 dark:text-navy-100 lg:px-5">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                    @foreach($leaveRequests as $request)
                                        <tr class="leave-row hover:bg-slate-50 dark:hover:bg-navy-600"
                                            data-type="{{ $request->leave_type }}" data-status="{{ $request->status }}">
                                            <td class="px-4 py-3 lg:px-5">
                                                <div class="flex items-center space-x-2">
                                                    <span
                                                        class="badge rounded-full {{ $request->leave_type === 'sick' ? 'bg-red-100 text-red-800' : ($request->leave_type === 'personal' ? 'bg-blue-100 text-blue-800' : ($request->leave_type === 'emergency' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                        {{ ucfirst($request->leave_type) }}
                                                    </span>
                                                    @if($request->is_emergency)
                                                        <span class="badge rounded-full bg-red-500 text-white text-xs">
                                                            Emergency
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <div class="text-sm">
                                                    <p class="text-slate-700 dark:text-navy-100">
                                                        {{ $request->start_date->format('M d, Y') }}
                                                    </p>
                                                    <p class="text-slate-500 dark:text-navy-300">
                                                        to {{ $request->end_date->format('M d, Y') }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                                    {{ $request->duration }} day{{ $request->duration > 1 ? 's' : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <p class="text-sm text-slate-600 dark:text-navy-200 max-w-xs truncate">
                                                    {{ $request->reason }}
                                                </p>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <span class="badge rounded-full {{ $request->status_badge_class }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 lg:px-5">
                                                <div class="flex space-x-2">
                                                    <button onclick="viewLeaveDetails({{ $request->id }})"
                                                        class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                        <i class="fa fa-eye text-slate-500"></i>
                                                    </button>
                                                    @if($request->status === 'pending')
                                                        <button onclick="editLeaveRequest({{ $request->id }})"
                                                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                                                            <i class="fa fa-edit text-slate-500"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-slate-400 dark:text-navy-300 mb-2">
                                <i class="fa fa-calendar-times text-4xl"></i>
                            </div>
                            <p class="text-slate-500 dark:text-navy-400">No leave requests found</p>
                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">Submit your first leave request</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterLeaveHistory(type) {
            const rows = document.querySelectorAll('.leave-row');
            rows.forEach(row => {
                if (type === 'all' || row.dataset.type === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function filterLeaveStatus(status) {
            const rows = document.querySelectorAll('.leave-row');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewLeaveDetails(requestId) {
            alert('View leave details for request ID: ' + requestId);
        }

        function editLeaveRequest(requestId) {
            alert('Edit leave request ID: ' + requestId);
        }
    </script>
@endsection