<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'company_id',
        'user_id',
        'program_id',
        'schedule_id',
        
        // Date & Time
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'break_start_time',
        'break_end_time',
        'hours_worked',
        'break_duration',
        'overtime_hours',
        
        // QR Code & Location
        'check_in_qr_code',
        'check_out_qr_code',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_location_name',
        'check_out_location_name',
        'check_in_device_id',
        'check_out_device_id',
        'check_in_ip_address',
        'check_out_ip_address',
        
        // Status
        'status',
        'attendance_type',
        'is_validated',
        'has_anomaly',
        'requires_approval',
        
        // Payroll
        'is_payable',
        'daily_rate_applied',
        'calculated_pay',
        'is_partial_day',
        'partial_day_percentage',
        
        // Notes & Exceptions
        'notes',
        'exception_reason',
        'anomaly_details',
        'weather_condition',
        'transport_issue',
        'venue_issue',
        
        // Proof Upload for Unauthorized Absences
        'proof_document_path',
        'proof_document_type',
        'proof_notes',
        'proof_uploaded_at',
        'proof_approved_by',
        'proof_approved_at',
        'proof_approval_notes',
        'proof_status',
        
        // Approval
        'validated_by',
        'validated_at',
        'validation_notes',
        'approved_by',
        'approved_at',
        'approval_notes',
        'processed_for_payroll_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s',
        'break_start_time' => 'datetime:H:i:s',
        'break_end_time' => 'datetime:H:i:s',
        'hours_worked' => 'decimal:2',
        'break_duration' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'is_validated' => 'boolean',
        'has_anomaly' => 'boolean',
        'requires_approval' => 'boolean',
        'is_payable' => 'boolean',
        'daily_rate_applied' => 'decimal:2',
        'calculated_pay' => 'decimal:2',
        'is_partial_day' => 'boolean',
        'partial_day_percentage' => 'decimal:2',
        'anomaly_details' => 'array',
        'transport_issue' => 'boolean',
        'venue_issue' => 'boolean',
        'proof_uploaded_at' => 'datetime',
        'proof_approved_at' => 'datetime',
        'validated_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_for_payroll_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate hours and detect anomalies on save
        static::saving(function (AttendanceRecord $record) {
            $record->calculateHours();
            $record->detectAnomalies();
            $record->calculatePay();
        });
    }

    /**
     * Get the company that owns this attendance record.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user (learner) this attendance record belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program this attendance is for.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the schedule this attendance is linked to.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the user who validated this attendance.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get the user who approved this attendance.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the device used for check-in.
     */
    public function checkInDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'check_in_device_id');
    }

    /**
     * Get the device used for check-out.
     */
    public function checkOutDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'check_out_device_id');
    }

    /**
     * Get the user who approved the proof document.
     */
    public function proofApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proof_approved_by');
    }

    /**
     * Calculate hours worked based on check-in and check-out times.
     */
    public function calculateHours(): void
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            $this->hours_worked = 0;
            return;
        }

        $checkIn = Carbon::parse($this->check_in_time);
        $checkOut = Carbon::parse($this->check_out_time);
        
        // Calculate total time
        $totalMinutes = $checkOut->diffInMinutes($checkIn);
        
        // Subtract break duration
        if ($this->break_start_time && $this->break_end_time) {
            $breakStart = Carbon::parse($this->break_start_time);
            $breakEnd = Carbon::parse($this->break_end_time);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            
            $this->break_duration = $breakMinutes / 60;
            $totalMinutes -= $breakMinutes;
        }

        $this->hours_worked = $totalMinutes / 60;

        // Calculate overtime (>8 hours per day)
        $this->overtime_hours = max(0, $this->hours_worked - 8);

        // Determine if this is a partial day
        if ($this->hours_worked < 4) {
            $this->is_partial_day = true;
            $this->partial_day_percentage = ($this->hours_worked / 8) * 100;
        } else {
            $this->is_partial_day = false;
            $this->partial_day_percentage = 100;
        }
    }

    /**
     * Detect attendance anomalies.
     */
    public function detectAnomalies(): void
    {
        $anomalies = [];

        // Missing check-out
        if ($this->check_in_time && !$this->check_out_time) {
            $anomalies[] = 'missing_check_out';
        }

        // Missing check-in
        if (!$this->check_in_time && $this->check_out_time) {
            $anomalies[] = 'missing_check_in';
        }

        // Excessive hours (>12 hours)
        if ($this->hours_worked > 12) {
            $anomalies[] = 'excessive_hours';
        }

        // Very short day (<1 hour)
        if ($this->hours_worked > 0 && $this->hours_worked < 1) {
            $anomalies[] = 'very_short_day';
        }

        // Late check-in (if schedule exists)
        if ($this->schedule && $this->check_in_time) {
            $scheduledStart = Carbon::parse($this->schedule->start_time);
            $actualCheckIn = Carbon::parse($this->check_in_time);
            
            if ($actualCheckIn->gt($scheduledStart->addMinutes($this->schedule->late_threshold_minutes ?? 15))) {
                $anomalies[] = 'late_arrival';
                $this->status = 'late';
            }
        }

        // Different devices for check-in and check-out
        if ($this->check_in_device_id && $this->check_out_device_id && 
            $this->check_in_device_id !== $this->check_out_device_id) {
            $anomalies[] = 'different_devices';
        }

        // Different locations for check-in and check-out (if both have GPS)
        if ($this->check_in_latitude && $this->check_in_longitude && 
            $this->check_out_latitude && $this->check_out_longitude) {
            $distance = $this->calculateDistance(
                $this->check_in_latitude, $this->check_in_longitude,
                $this->check_out_latitude, $this->check_out_longitude
            );
            
            if ($distance > 1) { // More than 1km difference
                $anomalies[] = 'location_mismatch';
            }
        }

        $this->has_anomaly = !empty($anomalies);
        $this->anomaly_details = $anomalies;

        // Set approval requirement for certain anomalies
        $requiresApproval = ['excessive_hours', 'very_short_day', 'missing_check_out'];
        $this->requires_approval = !empty(array_intersect($anomalies, $requiresApproval));
    }

    /**
     * Calculate pay for this attendance record.
     */
    public function calculatePay(): void
    {
        if (!$this->is_payable || !$this->program) {
            $this->calculated_pay = 0;
            return;
        }

        $dailyRate = $this->daily_rate_applied ?: $this->program->daily_rate;

        if ($this->is_partial_day) {
            // Pro-rata calculation for partial days
            $this->calculated_pay = $dailyRate * ($this->partial_day_percentage / 100);
        } else {
            // Full day pay
            $this->calculated_pay = $dailyRate;
        }

        // Add overtime pay if applicable
        if ($this->overtime_hours > 0) {
            $hourlyRate = $dailyRate / 8; // Assume 8-hour working day
            $this->calculated_pay += $this->overtime_hours * $hourlyRate * 1.5; // 1.5x for overtime
        }

        $this->daily_rate_applied = $dailyRate;
    }

    /**
     * Calculate distance between two GPS coordinates.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if attendance is complete (has both check-in and check-out).
     */
    public function isComplete(): bool
    {
        return $this->check_in_time && $this->check_out_time;
    }

    /**
     * Check if attendance is late.
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Check if attendance needs validation.
     */
    public function needsValidation(): bool
    {
        return !$this->is_validated && ($this->has_anomaly || $this->requires_approval);
    }

    /**
     * Check if attendance has been processed for payroll.
     */
    public function isProcessedForPayroll(): bool
    {
        return $this->processed_for_payroll_at !== null;
    }

    /**
     * Mark attendance as validated.
     */
    public function validate(User $validator, ?string $notes = null): void
    {
        $this->is_validated = true;
        $this->validated_by = $validator->id;
        $this->validated_at = now();
        $this->validation_notes = $notes;
        $this->save();
    }

    /**
     * Mark attendance as approved.
     */
    public function approve(User $approver, ?string $notes = null): void
    {
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->requires_approval = false;
        $this->save();
    }

    /**
     * Mark attendance as processed for payroll.
     */
    public function markProcessedForPayroll(): void
    {
        $this->processed_for_payroll_at = now();
        $this->save();
    }

    /**
     * Upload proof document for unauthorized absence.
     */
    public function uploadProof(string $documentPath, string $documentType, ?string $notes = null): void
    {
        $this->update([
            'proof_document_path' => $documentPath,
            'proof_document_type' => $documentType,
            'proof_notes' => $notes,
            'proof_uploaded_at' => now(),
            'proof_status' => 'pending',
        ]);
    }

    /**
     * Approve proof document and change status to authorized.
     */
    public function approveProof(User $approver, ?string $notes = null): void
    {
        $this->update([
            'status' => 'absent_authorized',
            'proof_status' => 'approved',
            'proof_approved_by' => $approver->id,
            'proof_approved_at' => now(),
            'proof_approval_notes' => $notes,
            'is_payable' => true, // Authorized absences are payable
        ]);
    }

    /**
     * Reject proof document.
     */
    public function rejectProof(User $approver, ?string $notes = null): void
    {
        $this->update([
            'proof_status' => 'rejected',
            'proof_approved_by' => $approver->id,
            'proof_approved_at' => now(),
            'proof_approval_notes' => $notes,
        ]);
    }

    /**
     * Mark as absent (unauthorized) - default status for new absences.
     */
    public function markAbsentUnauthorized(?string $reason = null): void
    {
        $this->update([
            'status' => 'absent_unauthorized',
            'exception_reason' => $reason,
            'is_payable' => false, // Unauthorized absences are not payable
        ]);
    }

    /**
     * Mark as absent (authorized) - for pre-approved absences.
     */
    public function markAbsentAuthorized(?string $reason = null): void
    {
        $this->update([
            'status' => 'absent_authorized',
            'exception_reason' => $reason,
            'is_payable' => true, // Authorized absences are payable
        ]);
    }

    /**
     * Mark as present with QR code check-in.
     */
    public function markPresentWithQR(string $qrCode, ?Device $device = null): void
    {
        $this->update([
            'status' => 'present',
            'check_in_qr_code' => $qrCode,
            'check_in_time' => now(),
            'is_payable' => true,
        ]);

        if ($device) {
            $this->checkInDevice()->associate($device);
            $this->save();
        }
    }

    /**
     * Check if proof is required for this absence.
     */
    public function requiresProof(): bool
    {
        return $this->status === 'absent_unauthorized' && $this->proof_status !== 'approved';
    }

    /**
     * Check if proof is pending approval.
     */
    public function hasPendingProof(): bool
    {
        return $this->proof_status === 'pending';
    }

    /**
     * Check if proof has been approved.
     */
    public function hasApprovedProof(): bool
    {
        return $this->proof_status === 'approved';
    }

    /**
     * Check-in with device tracking.
     */
    public function checkIn(Device $device, ?array $location = null, ?string $qrCode = null): void
    {
        $this->check_in_time = now();
        $this->check_in_device_id = $device->id;
        $this->check_in_qr_code = $qrCode;
        $this->check_in_ip_address = request()->ip();
        
        if ($location) {
            $this->check_in_latitude = $location['latitude'] ?? null;
            $this->check_in_longitude = $location['longitude'] ?? null;
            $this->check_in_location_name = $location['name'] ?? null;
        }
        
        // Update device activity
        $device->updateActivity(request()->ip(), $location);
        
        $this->save();
    }

    /**
     * Check-out with device tracking.
     */
    public function checkOut(Device $device, ?array $location = null, ?string $qrCode = null): void
    {
        $this->check_out_time = now();
        $this->check_out_device_id = $device->id;
        $this->check_out_qr_code = $qrCode;
        $this->check_out_ip_address = request()->ip();
        
        if ($location) {
            $this->check_out_latitude = $location['latitude'] ?? null;
            $this->check_out_longitude = $location['longitude'] ?? null;
            $this->check_out_location_name = $location['name'] ?? null;
        }
        
        // Update device activity
        $device->updateActivity(request()->ip(), $location);
        
        // Mark as present if both check-in and check-out are complete
        if ($this->check_in_time) {
            $this->status = 'present';
        }
        
        $this->save();
    }

    /**
     * Validate device permissions for attendance.
     */
    public function canUseDevice(Device $device): bool
    {
        // Device must belong to the same user
        if ($device->user_id !== $this->user_id) {
            return false;
        }
        
        // Device must be active and not blocked
        if (!$device->is_active || $device->is_blocked) {
            return false;
        }
        
        // Device must not be locked
        if ($device->isLocked()) {
            return false;
        }
        
        // Device should support attendance features
        return $device->supportsAttendance();
    }

    /**
     * Get attendance status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'present' => 'green',
            'late' => 'yellow',
            'absent_unauthorized' => 'red',
            'absent_authorized' => 'orange',
            'excused' => 'blue',
            'on_leave' => 'purple',
            'sick' => 'orange',
            'half_day' => 'amber',
            'pending_approval' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope to get attendance records that need validation.
     */
    public function scopeNeedsValidation($query)
    {
        return $query->where('is_validated', false)
                     ->where(function ($q) {
                         $q->where('has_anomaly', true)
                           ->orWhere('requires_approval', true);
                     });
    }

    /**
     * Scope to get attendance records by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get payable attendance records.
     */
    public function scopePayable($query)
    {
        return $query->where('is_payable', true)
                     ->whereIn('status', ['present', 'late', 'half_day', 'absent_authorized']);
    }

    /**
     * Scope to get attendance records not yet processed for payroll.
     */
    public function scopeNotProcessedForPayroll($query)
    {
        return $query->whereNull('processed_for_payroll_at');
    }

    /**
     * Scope to get attendance by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get attendance records requiring proof.
     */
    public function scopeRequiringProof($query)
    {
        return $query->where('status', 'absent_unauthorized')
                     ->where('proof_status', '!=', 'approved');
    }

    /**
     * Scope to get attendance records with pending proof.
     */
    public function scopeWithPendingProof($query)
    {
        return $query->where('proof_status', 'pending');
    }

    /**
     * Scope to get unauthorized absences.
     */
    public function scopeUnauthorizedAbsences($query)
    {
        return $query->where('status', 'absent_unauthorized');
    }

    /**
     * Scope to get authorized absences.
     */
    public function scopeAuthorizedAbsences($query)
    {
        return $query->where('status', 'absent_authorized');
    }
}