<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\MobilePasswordResetNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'id_number',
        'birth_date',
        'gender',
        'marital_status',
        'citizenship_status',
        'employee_number',
        'employment_start_date',
        'employment_status',
        'employment_basis',
        'is_learner',
        'is_employee',
        'occupation',
        'bank_name',
        'bank_account_number',
        'bank_branch_code',
        'tax_number',
        'eti_eligible',
        'company_id',
        'res_addr_line1',
        'res_addr_line2',
        'res_suburb',
        'res_city',
        'res_postcode',
        'post_addr_line1',
        'post_addr_line2',
        'post_suburb',
        'post_city',
        'post_postcode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the company this user belongs to.
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Get the attendance records for this user.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(\App\Models\AttendanceRecord::class);
    }

    /**
     * Get the payslips for this user.
     */
    public function payslips()
    {
        return $this->hasMany(\App\Models\Payslip::class);
    }

    /**
     * Get the programs this user is enrolled in.
     */
    public function programs()
    {
        return $this->belongsToMany(\App\Models\Program::class, 'program_learners');
    }

    /**
     * Get the program learner enrollments for this user.
     */
    public function programLearners()
    {
        return $this->hasMany(\App\Models\ProgramLearner::class);
    }

    /**
     * Get the active program enrollments for this user.
     */
    public function activeProgramLearners()
    {
        return $this->hasMany(\App\Models\ProgramLearner::class)->active();
    }

    /**
     * Get the enrolled programs for this user.
     */
    public function enrolledPrograms()
    {
        return $this->belongsToMany(\App\Models\Program::class, 'program_learners')
            ->wherePivotIn('status', ['enrolled', 'active'])
            ->withPivot([
                'enrollment_date',
                'completion_date',
                'status',
                'eti_eligible',
                'eti_monthly_amount',
                'attendance_percentage',
                'notes'
            ]);
    }

    /**
     * Get the schedules this user is associated with.
     */
    public function schedules()
    {
        return $this->hasMany(\App\Models\Schedule::class);
    }

    /**
     * Get the SIM card allocations for this user.
     */
    public function simCardAllocations()
    {
        return $this->hasMany(\App\Models\SimCardAllocation::class);
    }

    /**
     * Get the active SIM card allocations for this user.
     */
    public function activeSimCardAllocations()
    {
        return $this->hasMany(\App\Models\SimCardAllocation::class)->active();
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MobilePasswordResetNotification($token));
    }

    /**
     * Check if the user belongs to a specific company.
     */
    public function belongsToCompany($companyId): bool
    {
        return $this->company_id === $companyId;
    }

    /**
     * Check if the user belongs to any company.
     */
    public function hasCompany(): bool
    {
        return !is_null($this->company_id);
    }

    /**
     * Get the user's company name.
     */
    public function getCompanyName(): ?string
    {
        return $this->company?->name;
    }

    /**
     * Scope to filter users by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
