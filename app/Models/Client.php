<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'status',
        'parent_client_id',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the client.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent client (for sub-clients).
     */
    public function parentClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'parent_client_id');
    }

    /**
     * Get the sub-clients of this client.
     */
    public function subClients(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_client_id');
    }

    /**
     * Get the user who created this client.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this client.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the programs for this client.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get all programs including sub-client programs.
     */
    public function allPrograms(): HasManyThrough
    {
        return $this->hasManyThrough(Program::class, Client::class, 'parent_client_id', 'client_id');
    }

    /**
     * Scope for active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for parent clients (no parent_client_id).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_client_id');
    }

    /**
     * Scope for sub-clients (has parent_client_id).
     */
    public function scopeSubClients($query)
    {
        return $query->whereNotNull('parent_client_id');
    }

    /**
     * Check if this is a parent client.
     */
    public function isParent(): bool
    {
        return is_null($this->parent_client_id);
    }

    /**
     * Check if this is a sub-client.
     */
    public function isSubClient(): bool
    {
        return !is_null($this->parent_client_id);
    }

    /**
     * Get the full hierarchy path.
     */
    public function getHierarchyPath(): string
    {
        $path = [$this->name];
        $parent = $this->parentClient;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parentClient;
        }

        return implode(' > ', $path);
    }

    /**
     * Get all sub-clients recursively.
     */
    public function getAllSubClients(): \Illuminate\Database\Eloquent\Collection
    {
        $subClients = $this->subClients;

        foreach ($this->subClients as $subClient) {
            $subClients = $subClients->merge($subClient->getAllSubClients());
        }

        return $subClients;
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
            default => 'gray',
        };
    }
}
