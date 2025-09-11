<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SimCardAllocation extends Model
{
    use HasFactory, HasTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sim_card_id',
        'user_id',
        'program_id',
        'company_id',
        'allocated_date',
        'return_date',
        'status',
        'charge_amount',
        'payment_required',
        'payment_status',
        'allocated_by',
        'returned_by',
        'notes',
        'return_notes',
        'conditions_on_allocation',
        'conditions_on_return',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'allocated_date' => 'date',
        'return_date' => 'date',
        'charge_amount' => 'decimal:2',
        'payment_required' => 'boolean',
        'conditions_on_allocation' => 'array',
        'conditions_on_return' => 'array',
    ];

    /**
     * Get the SIM card that was allocated.
     */
    public function simCard(): BelongsTo
    {
        return $this->belongsTo(SimCard::class);
    }

    /**
     * Get the user (learner) who received the allocation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program associated with this allocation.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the company this allocation belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who allocated the SIM card.
     */
    public function allocator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    /**
     * Get the user who processed the return.
     */
    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Check if the allocation is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the SIM card has been returned.
     */
    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    /**
     * Check if payment is overdue.
     */
    public function isPaymentOverdue(): bool
    {
        return $this->payment_required && $this->payment_status === 'overdue';
    }

    /**
     * Get the duration of the allocation in days.
     */
    public function getDurationDays(): int
    {
        $endDate = $this->return_date ?? now();
        return $this->allocated_date->diffInDays($endDate);
    }

    /**
     * Scope to get active allocations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get returned allocations.
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope to filter by program.
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to filter by learner.
     */
    public function scopeForLearner($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'returned' => 'blue',
            'lost' => 'red',
            'damaged' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get the payment status color for UI display.
     */
    public function getPaymentStatusColor(): string
    {
        return match ($this->payment_status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'overdue' => 'red',
            'waived' => 'blue',
            default => 'gray',
        };
    }
}
