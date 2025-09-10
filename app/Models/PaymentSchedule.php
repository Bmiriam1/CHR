<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentSchedule extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'company_id',
        'program_id',
        
        // Schedule identification
        'schedule_number',
        'title',
        'frequency',
        
        // Period definition
        'period_start_date',
        'period_end_date',
        'payment_due_date',
        'attendance_cutoff_date',
        'year',
        'period_number',
        'week_number',
        'month_number',
        
        // Attendance summary
        'learner_summary',
        'total_learners',
        'total_days_worked',
        'total_hours_worked',
        'total_overtime_hours',
        
        // Payment calculations
        'total_basic_earnings',
        'total_overtime_pay',
        'total_allowances',
        'total_gross_pay',
        'total_paye_tax',
        'total_uif_employee',
        'total_uif_employer',
        'total_sdl_contribution',
        'total_eti_benefit',
        'total_deductions',
        'total_net_pay',
        
        // Employer costs
        'employer_total_cost',
        'employer_uif_cost',
        'employer_sdl_cost',
        'employer_eti_saving',
        
        // Status & processing
        'status',
        'is_final',
        'includes_overtime',
        'includes_allowances',
        
        // Export functionality
        'export_formats',
        'last_exported_at',
        'export_file_path',
        'export_settings',
        
        // Approval workflow
        'created_by',
        'calculated_by',
        'calculated_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'exported_by',
        'exported_at',
        
        // Compliance & audit
        'calculation_metadata',
        'attendance_filters',
        'calculation_hash',
        'attendance_records_count',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'payment_due_date' => 'date',
        'attendance_cutoff_date' => 'date',
        'learner_summary' => 'array',
        'total_days_worked' => 'decimal:2',
        'total_hours_worked' => 'decimal:2',
        'total_overtime_hours' => 'decimal:2',
        'total_basic_earnings' => 'decimal:2',
        'total_overtime_pay' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_gross_pay' => 'decimal:2',
        'total_paye_tax' => 'decimal:2',
        'total_uif_employee' => 'decimal:2',
        'total_uif_employer' => 'decimal:2',
        'total_sdl_contribution' => 'decimal:2',
        'total_eti_benefit' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net_pay' => 'decimal:2',
        'employer_total_cost' => 'decimal:2',
        'employer_uif_cost' => 'decimal:2',
        'employer_sdl_cost' => 'decimal:2',
        'employer_eti_saving' => 'decimal:2',
        'is_final' => 'boolean',
        'includes_overtime' => 'boolean',
        'includes_allowances' => 'boolean',
        'export_formats' => 'array',
        'last_exported_at' => 'datetime',
        'export_settings' => 'array',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'exported_at' => 'datetime',
        'calculation_metadata' => 'array',
        'attendance_filters' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (PaymentSchedule $schedule) {
            if (!$schedule->schedule_number) {
                $schedule->schedule_number = $schedule->generateScheduleNumber();
            }
            
            if (!$schedule->title) {
                $schedule->title = $schedule->generateTitle();
            }
        });

        static::saving(function (PaymentSchedule $schedule) {
            if ($schedule->isDirty([
                'total_basic_earnings', 'total_overtime_pay', 'total_allowances',
                'total_paye_tax', 'total_uif_employee', 'total_uif_employer',
                'total_sdl_contribution', 'total_eti_benefit'
            ])) {
                $schedule->calculateTotals();
            }
        });
    }

    /**
     * Get the company this schedule belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the program this schedule is for.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who created this schedule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who calculated this schedule.
     */
    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * Get the user who approved this schedule.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who exported this schedule.
     */
    public function exporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exported_by');
    }

    /**
     * Generate unique schedule number.
     */
    protected function generateScheduleNumber(): string
    {
        $prefix = match($this->frequency) {
            'weekly' => 'WKL',
            'bi_weekly' => 'BWK',
            'monthly' => 'MTH',
            default => 'PAY',
        };
        
        $year = $this->year ?? now()->year;
        $period = str_pad($this->period_number ?? 1, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$year}-{$period}-{$random}";
    }

    /**
     * Generate schedule title.
     */
    protected function generateTitle(): string
    {
        $frequency = match($this->frequency) {
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            default => 'Payment',
        };
        
        $period = $this->period_start_date?->format('M Y') ?? now()->format('M Y');
        
        return "{$frequency} Payment Schedule - {$period}";
    }

    /**
     * Calculate totals and employer costs.
     */
    protected function calculateTotals(): void
    {
        // Calculate total gross pay
        $this->total_gross_pay = $this->total_basic_earnings + 
                                $this->total_overtime_pay + 
                                $this->total_allowances;

        // Calculate total deductions
        $this->total_deductions = $this->total_paye_tax + 
                                 $this->total_uif_employee;

        // Calculate total net pay
        $this->total_net_pay = $this->total_gross_pay - $this->total_deductions;

        // Calculate employer costs
        $this->employer_uif_cost = $this->total_uif_employer;
        $this->employer_sdl_cost = $this->total_sdl_contribution;
        $this->employer_eti_saving = $this->total_eti_benefit;

        // Total employer cost = Gross pay + Employer UIF + SDL - ETI benefits
        $this->employer_total_cost = $this->total_gross_pay + 
                                   $this->employer_uif_cost + 
                                   $this->employer_sdl_cost - 
                                   $this->employer_eti_saving;
    }

    /**
     * Generate schedule from attendance records.
     */
    public static function generateFromAttendance(
        Company $company, 
        Program $program, 
        string $frequency, 
        Carbon $startDate, 
        Carbon $endDate,
        ?User $creator = null
    ): self {
        // Calculate period details
        $year = $startDate->year;
        $periodNumber = self::calculatePeriodNumber($frequency, $startDate);
        
        // Get attendance records for the period
        $attendanceRecords = AttendanceRecord::where('company_id', $company->id)
            ->where('program_id', $program->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->whereIn('status', ['present', 'late', 'half_day', 'absent_authorized'])
            ->where('is_payable', true)
            ->with(['user', 'program'])
            ->get();

        // Group by learner and calculate totals
        $learnerSummary = [];
        $totals = [
            'learners' => 0,
            'days_worked' => 0,
            'hours_worked' => 0,
            'overtime_hours' => 0,
            'basic_earnings' => 0,
            'overtime_pay' => 0,
            'allowances' => 0,
            'paye_tax' => 0,
            'uif_employee' => 0,
            'uif_employer' => 0,
            'sdl_contribution' => 0,
            'eti_benefit' => 0,
        ];

        foreach ($attendanceRecords->groupBy('user_id') as $userId => $records) {
            $user = $records->first()->user;
            $userTotals = [
                'user_id' => $userId,
                'name' => $user->name,
                'employee_number' => $user->employee_number,
                'days_worked' => 0,
                'hours_worked' => 0,
                'overtime_hours' => 0,
                'basic_earnings' => 0,
                'overtime_pay' => 0,
                'gross_pay' => 0,
                'paye_tax' => 0,
                'uif_employee' => 0,
                'net_pay' => 0,
            ];

            foreach ($records as $record) {
                // Calculate days (partial days count as decimal)
                if ($record->is_partial_day) {
                    $userTotals['days_worked'] += $record->partial_day_percentage / 100;
                } else {
                    $userTotals['days_worked'] += 1;
                }

                $userTotals['hours_worked'] += $record->hours_worked;
                $userTotals['overtime_hours'] += $record->overtime_hours;
                $userTotals['basic_earnings'] += $record->calculated_pay - ($record->overtime_hours * ($record->daily_rate_applied / 8) * 1.5);
                $userTotals['overtime_pay'] += $record->overtime_hours * ($record->daily_rate_applied / 8) * 1.5;
            }

            // Calculate tax and UIF for this user
            $userTotals['gross_pay'] = $userTotals['basic_earnings'] + $userTotals['overtime_pay'];
            
            // Simplified PAYE calculation (should use proper tax tables in production)
            $userTotals['paye_tax'] = max(0, ($userTotals['gross_pay'] - 4050) * 0.18); // Simplified
            
            // UIF calculation (1% each for employee and employer, max R177.12 per month)
            $uifBase = min($userTotals['gross_pay'], 17712); // UIF cap
            $userTotals['uif_employee'] = $uifBase * 0.01;
            
            $userTotals['net_pay'] = $userTotals['gross_pay'] - $userTotals['paye_tax'] - $userTotals['uif_employee'];

            $learnerSummary[] = $userTotals;

            // Add to totals
            $totals['days_worked'] += $userTotals['days_worked'];
            $totals['hours_worked'] += $userTotals['hours_worked'];
            $totals['overtime_hours'] += $userTotals['overtime_hours'];
            $totals['basic_earnings'] += $userTotals['basic_earnings'];
            $totals['overtime_pay'] += $userTotals['overtime_pay'];
            $totals['paye_tax'] += $userTotals['paye_tax'];
            $totals['uif_employee'] += $userTotals['uif_employee'];
            $totals['uif_employer'] += $userTotals['uif_employee']; // Same amount
        }

        $totals['learners'] = count($learnerSummary);
        
        // Calculate SDL (1% of total payroll)
        $totals['sdl_contribution'] = ($totals['basic_earnings'] + $totals['overtime_pay']) * 0.01;

        // ETI calculation (simplified - would need proper ETI calculation in production)
        $totals['eti_benefit'] = 0; // Would need to implement ETI calculation

        // Create the payment schedule
        return self::create([
            'company_id' => $company->id,
            'program_id' => $program->id,
            'frequency' => $frequency,
            'period_start_date' => $startDate,
            'period_end_date' => $endDate,
            'payment_due_date' => self::calculatePaymentDueDate($frequency, $endDate),
            'attendance_cutoff_date' => $endDate,
            'year' => $year,
            'period_number' => $periodNumber,
            'week_number' => $frequency === 'weekly' ? $startDate->weekOfYear : null,
            'month_number' => $frequency === 'monthly' ? $startDate->month : null,
            'learner_summary' => $learnerSummary,
            'total_learners' => $totals['learners'],
            'total_days_worked' => $totals['days_worked'],
            'total_hours_worked' => $totals['hours_worked'],
            'total_overtime_hours' => $totals['overtime_hours'],
            'total_basic_earnings' => $totals['basic_earnings'],
            'total_overtime_pay' => $totals['overtime_pay'],
            'total_allowances' => 0, // Would calculate from allowances
            'total_paye_tax' => $totals['paye_tax'],
            'total_uif_employee' => $totals['uif_employee'],
            'total_uif_employer' => $totals['uif_employer'],
            'total_sdl_contribution' => $totals['sdl_contribution'],
            'total_eti_benefit' => $totals['eti_benefit'],
            'includes_overtime' => $totals['overtime_hours'] > 0,
            'includes_allowances' => false,
            'attendance_records_count' => $attendanceRecords->count(),
            'created_by' => $creator?->id ?? auth()->id(),
            'calculation_metadata' => [
                'calculated_at' => now(),
                'attendance_period' => [$startDate->toDateString(), $endDate->toDateString()],
                'calculation_method' => 'attendance_based',
            ],
            'attendance_filters' => [
                'status' => ['present', 'late', 'half_day', 'absent_authorized'],
                'is_payable' => true,
                'date_range' => [$startDate->toDateString(), $endDate->toDateString()],
            ],
        ]);
    }

    /**
     * Calculate period number based on frequency.
     */
    protected static function calculatePeriodNumber(string $frequency, Carbon $date): int
    {
        return match($frequency) {
            'weekly' => $date->weekOfYear,
            'bi_weekly' => ceil($date->weekOfYear / 2),
            'monthly' => $date->month,
            default => 1,
        };
    }

    /**
     * Calculate payment due date.
     */
    protected static function calculatePaymentDueDate(string $frequency, Carbon $endDate): Carbon
    {
        return match($frequency) {
            'weekly' => $endDate->copy()->addDays(3), // Pay 3 days after period end
            'bi_weekly' => $endDate->copy()->addDays(5), // Pay 5 days after period end
            'monthly' => $endDate->copy()->addDays(7), // Pay 7 days after period end
            default => $endDate->copy()->addDays(5),
        };
    }

    /**
     * Export schedule to specified format.
     */
    public function export(string $format, ?User $exporter = null): array
    {
        $exporter = $exporter ?? auth()->user();
        
        $this->update([
            'exported_by' => $exporter->id,
            'exported_at' => now(),
            'last_exported_at' => now(),
        ]);

        return match(strtolower($format)) {
            'pdf' => $this->exportToPdf(),
            'excel' => $this->exportToExcel(),
            'csv' => $this->exportToCsv(),
            'xml' => $this->exportToXml(),
            'json' => $this->exportToJson(),
            default => throw new \InvalidArgumentException("Unsupported export format: {$format}"),
        };
    }

    /**
     * Export to PDF format.
     */
    protected function exportToPdf(): array
    {
        // Implementation would use a PDF library like DomPDF or wkhtmltopdf
        return [
            'format' => 'pdf',
            'filename' => "{$this->schedule_number}.pdf",
            'content_type' => 'application/pdf',
            'data' => [], // PDF binary data would go here
        ];
    }

    /**
     * Export to Excel format.
     */
    protected function exportToExcel(): array
    {
        $data = [
            'schedule_info' => [
                'Schedule Number' => $this->schedule_number,
                'Title' => $this->title,
                'Company' => $this->company->company_name,
                'Program' => $this->program->program_name,
                'Period' => "{$this->period_start_date->format('Y-m-d')} to {$this->period_end_date->format('Y-m-d')}",
                'Payment Due' => $this->payment_due_date->format('Y-m-d'),
            ],
            'summary' => [
                'Total Learners' => $this->total_learners,
                'Total Days Worked' => $this->total_days_worked,
                'Total Hours Worked' => $this->total_hours_worked,
                'Total Basic Earnings' => $this->total_basic_earnings,
                'Total Overtime Pay' => $this->total_overtime_pay,
                'Total Gross Pay' => $this->total_gross_pay,
                'Total PAYE Tax' => $this->total_paye_tax,
                'Total UIF Employee' => $this->total_uif_employee,
                'Total Net Pay' => $this->total_net_pay,
                'Employer Total Cost' => $this->employer_total_cost,
            ],
            'learners' => $this->learner_summary,
        ];

        return [
            'format' => 'excel',
            'filename' => "{$this->schedule_number}.xlsx",
            'content_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'data' => $data,
        ];
    }

    /**
     * Export to CSV format.
     */
    protected function exportToCsv(): array
    {
        $csv = [];
        
        // Header row
        $csv[] = [
            'Employee Number', 'Name', 'Days Worked', 'Hours Worked', 
            'Overtime Hours', 'Basic Earnings', 'Overtime Pay', 'Gross Pay',
            'PAYE Tax', 'UIF Employee', 'Net Pay'
        ];

        // Learner rows
        foreach ($this->learner_summary as $learner) {
            $csv[] = [
                $learner['employee_number'] ?? '',
                $learner['name'],
                $learner['days_worked'],
                $learner['hours_worked'],
                $learner['overtime_hours'],
                $learner['basic_earnings'],
                $learner['overtime_pay'],
                $learner['gross_pay'],
                $learner['paye_tax'],
                $learner['uif_employee'],
                $learner['net_pay'],
            ];
        }

        return [
            'format' => 'csv',
            'filename' => "{$this->schedule_number}.csv",
            'content_type' => 'text/csv',
            'data' => $csv,
        ];
    }

    /**
     * Export to XML format.
     */
    protected function exportToXml(): array
    {
        // Implementation would generate XML structure
        return [
            'format' => 'xml',
            'filename' => "{$this->schedule_number}.xml",
            'content_type' => 'application/xml',
            'data' => '', // XML string would go here
        ];
    }

    /**
     * Export to JSON format.
     */
    protected function exportToJson(): array
    {
        return [
            'format' => 'json',
            'filename' => "{$this->schedule_number}.json",
            'content_type' => 'application/json',
            'data' => $this->toArray(),
        ];
    }

    /**
     * Approve the schedule.
     */
    public function approve(User $approver, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Mark schedule as final.
     */
    public function finalize(): void
    {
        $this->update([
            'is_final' => true,
            'status' => 'processed',
        ]);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'calculated' => 'blue',
            'approved' => 'green',
            'exported' => 'purple',
            'processed' => 'emerald',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope to get schedules by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get schedules by frequency.
     */
    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Scope to get schedules for a specific year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get schedules due for payment.
     */
    public function scopeDueForPayment($query)
    {
        return $query->where('payment_due_date', '<=', now()->toDateString())
                     ->whereIn('status', ['approved', 'exported']);
    }

    /**
     * Scope to get finalized schedules.
     */
    public function scopeFinal($query)
    {
        return $query->where('is_final', true);
    }
}