<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'company_id',
        'leave_type',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'is_emergency',
        'attachment_path',
        'requires_medical_certificate',
        'medical_certificate_path',
        'medical_certificate_date',
        'medical_practitioner_name',
        'medical_practitioner_practice_number',
        'is_paid_leave',
        'daily_rate_at_time',
        'total_leave_pay',
        'affects_tax_calculation',
        'tax_deduction_amount',
        'is_advance_leave',
        'return_to_work_date',
        'return_to_work_notes',
        'approval_workflow',
        'current_approval_step',
        'submitted_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'submitted_at' => 'datetime',
        'medical_certificate_date' => 'date',
        'return_to_work_date' => 'date',
        'is_emergency' => 'boolean',
        'requires_medical_certificate' => 'boolean',
        'is_paid_leave' => 'boolean',
        'affects_tax_calculation' => 'boolean',
        'is_advance_leave' => 'boolean',
        'daily_rate_at_time' => 'decimal:2',
        'total_leave_pay' => 'decimal:2',
        'tax_deduction_amount' => 'decimal:2',
        'approval_workflow' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
