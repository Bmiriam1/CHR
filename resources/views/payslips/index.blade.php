@extends('layouts.app')

@section('title', 'Payslips')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payslip Management</h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
                        <i class="fas fa-plus"></i> Generate Payslips
                    </button>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                        <i class="fas fa-layer-group"></i> Bulk Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R {{ number_format($monthlyStats['total_paid'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingApproval ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">PAYE Tax</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R {{ number_format($monthlyStats['total_paye'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Payslip number or employee...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="calculated" {{ request('status') === 'calculated' ? 'selected' : '' }}>Calculated</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="month" class="form-label">Month</label>
                    <select class="form-select" id="month" name="month">
                        <option value="">All Months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        <option value="">All Years</option>
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('payslips.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payslips Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Payslip #</th>
                            <th>Employee</th>
                            <th>Program</th>
                            <th>Period</th>
                            <th>Gross Pay</th>
                            <th>PAYE Tax</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payslips ?? [] as $payslip)
                            <tr>
                                <td>
                                    <strong>{{ $payslip->payslip_number }}</strong>
                                    <div class="small text-muted">{{ $payslip->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <div>
                                        {{ $payslip->user->name }}
                                        <div class="small text-muted">{{ $payslip->user->employee_number }}</div>
                                    </div>
                                </td>
                                <td>{{ $payslip->program->program_name }}</td>
                                <td>
                                    {{ $payslip->payroll_period_start->format('M d') }} - 
                                    {{ $payslip->payroll_period_end->format('M d, Y') }}
                                </td>
                                <td>R {{ number_format($payslip->gross_earnings, 2) }}</td>
                                <td>R {{ number_format($payslip->paye_tax, 2) }}</td>
                                <td>
                                    <strong>R {{ number_format($payslip->net_pay, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $payslip->getStatusColor() }}">
                                        {{ ucfirst($payslip->status) }}
                                    </span>
                                    @if($payslip->is_final)
                                        <div><small class="badge bg-success">Final</small></div>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('payslips.show', $payslip) }}">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('payslips.download', $payslip) }}" target="_blank">
                                                    <i class="fas fa-download"></i> Download PDF
                                                </a>
                                            </li>
                                            @if($payslip->status === 'calculated' && !$payslip->is_final)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('payslips.approve', $payslip) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @if($payslip->status === 'approved' && !$payslip->is_final)
                                                <li>
                                                    <form method="POST" action="{{ route('payslips.process', $payslip) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-primary">
                                                            <i class="fas fa-cogs"></i> Process Payment
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @if(!$payslip->is_final)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('payslips.edit', $payslip) }}">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <p>No payslips found</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
                                            Generate Your First Payslip
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($payslips) && $payslips->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $payslips->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Generate Payslip Modal -->
<div class="modal fade" id="generatePayslipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('payslips.generate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Payslips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="generate_company_id" class="form-label required">Company</label>
                            <select class="form-select" id="generate_company_id" name="company_id" required>
                                <option value="">Select Company</option>
                                <!-- Companies would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="generate_program_id" class="form-label">Program (Optional)</label>
                            <select class="form-select" id="generate_program_id" name="program_id">
                                <option value="">All Programs</option>
                                <!-- Programs would be populated based on company selection -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payroll_period_start" class="form-label required">Period Start</label>
                            <input type="date" class="form-control" id="payroll_period_start" 
                                   name="payroll_period_start" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payroll_period_end" class="form-label required">Period End</label>
                            <input type="date" class="form-control" id="payroll_period_end" 
                                   name="payroll_period_end" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pay_date" class="form-label required">Pay Date</label>
                        <input type="date" class="form-control" id="pay_date" name="pay_date" required>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> This will generate payslips for all eligible learners with attendance records in the specified period.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cogs"></i> Generate Payslips
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div class="modal fade" id="bulkGenerateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('payslips.bulk-generate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Generate Payslips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_companies" class="form-label">Select Companies</label>
                        <div class="form-check-group" style="max-height: 200px; overflow-y: auto;">
                            <!-- Companies checkboxes would be populated here -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="company_1">
                                <label class="form-check-label" for="company_1">Company Name 1</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bulk_period_start" class="form-label required">Period Start</label>
                            <input type="date" class="form-control" id="bulk_period_start" 
                                   name="payroll_period_start" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bulk_period_end" class="form-label required">Period End</label>
                            <input type="date" class="form-control" id="bulk_period_end" 
                                   name="payroll_period_end" required>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This will generate payslips for ALL selected companies. Please ensure attendance data is complete.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-layer-group"></i> Bulk Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: red;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set pay date to end of period + 5 days
    const periodEndInput = document.getElementById('payroll_period_end');
    const payDateInput = document.getElementById('pay_date');
    
    if (periodEndInput && payDateInput) {
        periodEndInput.addEventListener('change', function() {
            if (this.value) {
                const endDate = new Date(this.value);
                endDate.setDate(endDate.getDate() + 5);
                
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                
                payDateInput.value = `${year}-${month}-${day}`;
            }
        });
    }
});
</script>
@endpush
@endsection