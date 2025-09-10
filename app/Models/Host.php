<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Host extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'program_id',
        'name',
        'code',
        'description',
        'address_line1',
        'address_line2',
        'city',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'radius_meters',
        'contact_person',
        'contact_phone',
        'contact_email',
        'qr_code',
        'qr_code_data',
        'qr_code_generated_at',
        'is_active',
        'requires_gps_validation',
        'requires_time_validation',
        'check_in_start_time',
        'check_in_end_time',
        'check_out_start_time',
        'check_out_end_time',
        'max_daily_check_ins',
        'allow_multiple_check_ins',
        'require_supervisor_approval',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'decimal:2',
        'qr_code_generated_at' => 'datetime',
        'is_active' => 'boolean',
        'requires_gps_validation' => 'boolean',
        'requires_time_validation' => 'boolean',
        'check_in_start_time' => 'datetime:H:i:s',
        'check_in_end_time' => 'datetime:H:i:s',
        'check_out_start_time' => 'datetime:H:i:s',
        'check_out_end_time' => 'datetime:H:i:s',
        'max_daily_check_ins' => 'integer',
        'allow_multiple_check_ins' => 'boolean',
        'require_supervisor_approval' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Host $host) {
            if (!$host->code) {
                $host->code = $host->generateCode();
            }
            if (!$host->qr_code) {
                $host->generateQRCode();
            }
        });
    }

    /**
     * Get the company this host belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the program this host is for.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get attendance records for this host.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'check_in_qr_code', 'qr_code');
    }

    /**
     * Generate unique host code.
     */
    protected function generateCode(): string
    {
        $prefix = 'HOST';
        $random = strtoupper(Str::random(6));

        // Ensure uniqueness
        while (static::where('code', $prefix . $random)->exists()) {
            $random = strtoupper(Str::random(6));
        }

        return $prefix . $random;
    }

    /**
     * Generate QR code for this host.
     */
    public function generateQRCode(): void
    {
        $qrData = [
            'type' => 'attendance_host',
            'host_id' => $this->id,
            'host_code' => $this->code,
            'program_id' => $this->program_id,
            'company_id' => $this->company_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius_meters,
            'generated_at' => now()->toISOString(),
        ];

        $this->qr_code = 'HOST_' . $this->code . '_' . now()->format('YmdHis');
        $this->qr_code_data = json_encode($qrData);
        $this->qr_code_generated_at = now();
    }

    /**
     * Validate GPS location against host location.
     */
    public function validateLocation(float $latitude, float $longitude): bool
    {
        if (!$this->requires_gps_validation) {
            return true;
        }

        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );

        return $distance <= $this->radius_meters;
    }

    /**
     * Validate check-in time against host time restrictions.
     */
    public function validateCheckInTime(?Carbon $checkInTime = null): bool
    {
        if (!$this->requires_time_validation) {
            return true;
        }

        $checkInTime = $checkInTime ?? now();

        if ($this->check_in_start_time && $checkInTime->format('H:i:s') < $this->check_in_start_time) {
            return false;
        }

        if ($this->check_in_end_time && $checkInTime->format('H:i:s') > $this->check_in_end_time) {
            return false;
        }

        return true;
    }

    /**
     * Validate check-out time against host time restrictions.
     */
    public function validateCheckOutTime(?Carbon $checkOutTime = null): bool
    {
        if (!$this->requires_time_validation) {
            return true;
        }

        $checkOutTime = $checkOutTime ?? now();

        if ($this->check_out_start_time && $checkOutTime->format('H:i:s') < $this->check_out_start_time) {
            return false;
        }

        if ($this->check_out_end_time && $checkOutTime->format('H:i:s') > $this->check_out_end_time) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can check in at this host.
     */
    public function canCheckIn(User $user, ?Carbon $checkInTime = null): array
    {
        $errors = [];

        // Check if host is active
        if (!$this->is_active) {
            $errors[] = 'Host location is not active';
        }

        // Check time validation
        if (!$this->validateCheckInTime($checkInTime)) {
            $errors[] = 'Check-in time is outside allowed hours';
        }

        // Check daily check-in limit
        if (!$this->allow_multiple_check_ins) {
            $today = ($checkInTime ?? now())->toDateString();
            $existingCheckIns = AttendanceRecord::where('user_id', $user->id)
                ->where('company_id', $this->company_id)
                ->where('attendance_date', $today)
                ->where('check_in_qr_code', $this->qr_code)
                ->count();

            if ($existingCheckIns >= $this->max_daily_check_ins) {
                $errors[] = 'Daily check-in limit exceeded for this host';
            }
        }

        return $errors;
    }

    /**
     * Check if user can check out at this host.
     */
    public function canCheckOut(User $user, ?Carbon $checkOutTime = null): array
    {
        $errors = [];

        // Check if host is active
        if (!$this->is_active) {
            $errors[] = 'Host location is not active';
        }

        // Check time validation
        if (!$this->validateCheckOutTime($checkOutTime)) {
            $errors[] = 'Check-out time is outside allowed hours';
        }

        // Check if user has checked in today
        $today = ($checkOutTime ?? now())->toDateString();
        $hasCheckedIn = AttendanceRecord::where('user_id', $user->id)
            ->where('company_id', $this->company_id)
            ->where('attendance_date', $today)
            ->where('check_in_qr_code', $this->qr_code)
            ->whereNotNull('check_in_time')
            ->exists();

        if (!$hasCheckedIn) {
            $errors[] = 'Must check in before checking out';
        }

        return $errors;
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get full address string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address_line1;

        if ($this->address_line2) {
            $address .= ', ' . $this->address_line2;
        }

        $address .= ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code;

        if ($this->country !== 'South Africa') {
            $address .= ', ' . $this->country;
        }

        return $address;
    }

    /**
     * Get QR code data as array.
     */
    public function getQRCodeDataArrayAttribute(): array
    {
        return json_decode($this->qr_code_data, true) ?? [];
    }

    /**
     * Scope to get active hosts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get hosts for a specific program.
     */
    public function scopeForProgram($query, int $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to get hosts within a radius of a location.
     */
    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusMeters = 1000)
    {
        return $query->selectRaw('*, 
            (6371000 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusMeters)
            ->orderBy('distance');
    }

    /**
     * Find host by QR code.
     */
    public static function findByQRCode(string $qrCode): ?self
    {
        return static::where('qr_code', $qrCode)->first();
    }

    /**
     * Validate QR code and return host if valid.
     */
    public static function validateQRCode(string $qrCode, ?array $location = null): array
    {
        $host = static::findByQRCode($qrCode);

        if (!$host) {
            return ['valid' => false, 'error' => 'Invalid QR code', 'host' => null];
        }

        if (!$host->is_active) {
            return ['valid' => false, 'error' => 'Host location is not active', 'host' => $host];
        }

        if ($location && !$host->validateLocation($location['latitude'], $location['longitude'])) {
            return ['valid' => false, 'error' => 'Location is outside allowed radius', 'host' => $host];
        }

        return ['valid' => true, 'error' => null, 'host' => $host];
    }
}
