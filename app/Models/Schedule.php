<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Schedule extends Model
{
    use HasFactory, HasTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'company_id',
        'program_id',
        'instructor_id',

        // Schedule details
        'title',
        'description',
        'session_code',
        'session_date',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'planned_duration_hours',
        'break_duration_hours',
        'net_training_hours',

        // Recurrence
        'recurrence_type',
        'recurrence_pattern',
        'recurrence_end_date',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',

        // Location & venue
        'venue_name',
        'venue_address',
        'room_number',
        'building',
        'campus',
        'is_online',
        'meeting_url',
        'meeting_id',
        'meeting_password',
        'platform',
        'venue_latitude',
        'venue_longitude',
        'geofence_radius',

        // QR Code
        'qr_code_content',
        'qr_code_path',
        'qr_code_active',
        'qr_code_valid_from',
        'qr_code_valid_until',

        // Curriculum
        'module_name',
        'unit_standard',
        'learning_outcomes',
        'assessment_criteria',
        'required_materials',

        // Attendance tracking
        'expected_attendees',
        'actual_attendees',
        'attendance_rate',
        'check_in_opens_at',
        'check_in_closes_at',
        'allow_late_check_in',
        'late_threshold_minutes',

        // Status
        'status',
        'session_type',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'rescheduled_from_id',

        // Notifications
        'send_reminders',
        'reminder_settings',
        'reminder_sent_at',

        // System
        'created_by',
        'additional_settings',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'break_start_time' => 'datetime:H:i:s',
        'break_end_time' => 'datetime:H:i:s',
        'planned_duration_hours' => 'decimal:2',
        'break_duration_hours' => 'decimal:2',
        'net_training_hours' => 'decimal:2',
        'recurrence_pattern' => 'array',
        'recurrence_end_date' => 'date',
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'saturday' => 'boolean',
        'sunday' => 'boolean',
        'is_online' => 'boolean',
        'venue_latitude' => 'decimal:8',
        'venue_longitude' => 'decimal:8',
        'qr_code_active' => 'boolean',
        'qr_code_valid_from' => 'datetime',
        'qr_code_valid_until' => 'datetime',
        'required_materials' => 'array',
        'attendance_rate' => 'decimal:2',
        'check_in_opens_at' => 'datetime:H:i:s',
        'check_in_closes_at' => 'datetime:H:i:s',
        'allow_late_check_in' => 'boolean',
        'cancelled_at' => 'datetime',
        'send_reminders' => 'boolean',
        'reminder_settings' => 'array',
        'reminder_sent_at' => 'datetime',
        'additional_settings' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate QR code and calculate duration on create
        static::creating(function (Schedule $schedule) {
            if (!$schedule->session_code) {
                $schedule->session_code = $schedule->generateSessionCode();
            }

            if (!$schedule->qr_code_content) {
                $schedule->qr_code_content = $schedule->generateQrCodeContent();
            }

            $schedule->calculateDuration();
        });

        static::updating(function (Schedule $schedule) {
            $schedule->calculateDuration();
        });
    }

    /**
     * Get the company that owns this schedule.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the program this schedule belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the instructor for this session.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the user who created this schedule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who cancelled this schedule.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get the original schedule this was rescheduled from.
     */
    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'rescheduled_from_id');
    }

    /**
     * Get all attendance records for this schedule.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Generate a unique session code.
     */
    protected function generateSessionCode(): string
    {
        $prefix = $this->program?->program_code ?? 'SESS';
        $date = $this->session_date?->format('Ymd') ?? now()->format('Ymd');
        $random = Str::upper(Str::random(4));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Generate QR code content.
     */
    protected function generateQrCodeContent(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Calculate session duration.
     */
    protected function calculateDuration(): void
    {
        if (!$this->start_time || !$this->end_time) {
            return;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        // Ensure end time is after start time
        if ($end->lt($start)) {
            $end->addDay();
        }

        $totalMinutes = $start->diffInMinutes($end);
        $this->planned_duration_hours = $totalMinutes / 60;

        // Calculate break duration
        if ($this->break_start_time && $this->break_end_time) {
            $breakStart = Carbon::parse($this->break_start_time);
            $breakEnd = Carbon::parse($this->break_end_time);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);

            $this->break_duration_hours = $breakMinutes / 60;
            $this->net_training_hours = $this->planned_duration_hours - $this->break_duration_hours;
        } else {
            $this->break_duration_hours = 0;
            $this->net_training_hours = $this->planned_duration_hours;
        }
    }

    /**
     * Check if check-in is currently open.
     */
    public function isCheckInOpen(): bool
    {
        if ($this->status !== 'scheduled' && $this->status !== 'in_progress') {
            return false;
        }

        if (!$this->qr_code_active) {
            return false;
        }

        $now = now();

        // Check QR code validity window
        if ($this->qr_code_valid_from && $now->lt($this->qr_code_valid_from)) {
            return false;
        }

        if ($this->qr_code_valid_until && $now->gt($this->qr_code_valid_until)) {
            return false;
        }

        // Check session-specific check-in window
        if ($this->check_in_opens_at || $this->check_in_closes_at) {
            $sessionDate = $this->session_date;

            if ($this->check_in_opens_at) {
                $opensAt = $sessionDate->copy()->setTimeFromTimeString($this->check_in_opens_at);
                if ($now->lt($opensAt)) {
                    return false;
                }
            }

            if ($this->check_in_closes_at) {
                $closesAt = $sessionDate->copy()->setTimeFromTimeString($this->check_in_closes_at);
                if ($now->gt($closesAt)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if late check-in is allowed.
     */
    public function isLateCheckInAllowed(): bool
    {
        return $this->allow_late_check_in;
    }

    /**
     * Check if the session is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the session is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the session is online.
     */
    public function isOnline(): bool
    {
        return $this->is_online;
    }

    /**
     * Check if location validation is required.
     */
    public function requiresLocationValidation(): bool
    {
        return !$this->is_online &&
            $this->venue_latitude &&
            $this->venue_longitude &&
            $this->geofence_radius > 0;
    }

    /**
     * Validate if a location is within the geofence.
     */
    public function validateLocation(float $latitude, float $longitude): bool
    {
        if (!$this->requiresLocationValidation()) {
            return true; // No validation required
        }

        $distance = $this->calculateDistance(
            $this->venue_latitude,
            $this->venue_longitude,
            $latitude,
            $longitude
        );

        return $distance <= ($this->geofence_radius / 1000); // Convert meters to kilometers
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
     * Update attendance statistics.
     */
    public function updateAttendanceStats(): void
    {
        $totalAttendance = $this->attendanceRecords()
            ->whereIn('status', ['present', 'late'])
            ->count();

        $this->actual_attendees = $totalAttendance;

        if ($this->expected_attendees > 0) {
            $this->attendance_rate = ($totalAttendance / $this->expected_attendees) * 100;
        } else {
            $this->attendance_rate = 0;
        }

        $this->save();
    }

    /**
     * Cancel the session.
     */
    public function cancel(User $user, string $reason): void
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->cancelled_by = $user->id;
        $this->cancelled_at = now();
        $this->qr_code_active = false;
        $this->save();
    }

    /**
     * Mark session as in progress.
     */
    public function start(): void
    {
        $this->status = 'in_progress';
        $this->save();
    }

    /**
     * Mark session as completed.
     */
    public function complete(): void
    {
        $this->status = 'completed';
        $this->updateAttendanceStats();
        $this->save();
    }

    /**
     * Get session status color for UI.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'scheduled' => 'blue',
            'in_progress' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            'postponed' => 'yellow',
            'rescheduled' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get days of week as array.
     */
    public function getDaysOfWeek(): array
    {
        $days = [];

        if ($this->monday) $days[] = 'Monday';
        if ($this->tuesday) $days[] = 'Tuesday';
        if ($this->wednesday) $days[] = 'Wednesday';
        if ($this->thursday) $days[] = 'Thursday';
        if ($this->friday) $days[] = 'Friday';
        if ($this->saturday) $days[] = 'Saturday';
        if ($this->sunday) $days[] = 'Sunday';

        return $days;
    }

    /**
     * Scope to get today's schedules.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
    }

    /**
     * Scope to get schedules by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('session_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get active schedules.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    /**
     * Scope to get online sessions.
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Scope to get in-person sessions.
     */
    public function scopeInPerson($query)
    {
        return $query->where('is_online', false);
    }

    /**
     * Scope to get schedules by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
