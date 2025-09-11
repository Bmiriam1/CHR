<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimCard extends Model
{
    use HasFactory, HasTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'phone_number',
        'serial_number',
        'service_provider',
        'cost_price',
        'selling_price',
        'status',
        'is_active',
        'notes',
        'purchased_at',
        'activated_at',
        'deactivated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'purchased_at' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Get the company that owns this SIM card.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all allocations for this SIM card.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(SimCardAllocation::class);
    }

    /**
     * Get the current active allocation.
     */
    public function currentAllocation()
    {
        return $this->allocations()->where('status', 'active')->latest()->first();
    }

    /**
     * Check if the SIM card is currently allocated.
     */
    public function isAllocated(): bool
    {
        return $this->status === 'allocated';
    }

    /**
     * Check if the SIM card is available for allocation.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Scope to get available SIM cards.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope to get allocated SIM cards.
     */
    public function scopeAllocated($query)
    {
        return $query->where('status', 'allocated');
    }

    /**
     * Scope to filter by service provider.
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('service_provider', $provider);
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'available' => 'green',
            'allocated' => 'blue',
            'deactivated' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMargin(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }

        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }
}
