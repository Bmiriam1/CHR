<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\AttendanceRecord;
use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckInForm extends Component
{
    public ?Schedule $schedule = null;
    public $scheduleId;
    
    // Check-in data
    public $qrCode;
    public $deviceId;
    public $latitude;
    public $longitude;
    public $locationName;
    
    // Form state
    public $showQrInput = false;
    public $showLocationInput = false;
    public $isCheckingIn = false;
    public $checkInCompleted = false;
    public $errorMessage = '';
    public $successMessage = '';
    
    // User and program info (accessed through schedule)
    public $user;
    public $program;
    public $attendanceRecord;
    
    // Available devices for user
    public $userDevices;

    public function mount(?int $scheduleId = null)
    {
        $this->user = auth()->user();
        
        if ($scheduleId) {
            $this->scheduleId = $scheduleId;
            $this->loadSchedule();
        }
        
        $this->loadUserDevices();
    }
    
    protected function loadSchedule()
    {
        $this->schedule = Schedule::with(['program', 'company'])
            ->where('id', $this->scheduleId)
            ->first();
            
        if (!$this->schedule) {
            $this->errorMessage = 'Schedule not found.';
            return;
        }
        
        // Access program information through the schedule
        $this->program = $this->schedule->program;
        
        Log::debug('Loaded schedule for check-in', [
            'schedule_id' => $this->schedule->id,
            'program_id' => $this->program?->id,
            'program_title' => $this->program?->title,
            'session_date' => $this->schedule->session_date,
            'session_time' => $this->schedule->start_time . ' - ' . $this->schedule->end_time
        ]);
        
        // Check if check-in is currently allowed
        if (!$this->schedule->isCheckInOpen()) {
            $this->errorMessage = 'Check-in is not currently open for this session.';
            return;
        }
        
        // Load or create attendance record for today
        $this->loadOrCreateAttendanceRecord();
    }
    
    protected function loadUserDevices()
    {
        $this->userDevices = Device::where('user_id', $this->user->id)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->get();
    }
    
    protected function loadOrCreateAttendanceRecord()
    {
        $today = now()->format('Y-m-d');
        
        $this->attendanceRecord = AttendanceRecord::where('user_id', $this->user->id)
            ->where('schedule_id', $this->schedule->id)
            ->where('attendance_date', $today)
            ->first();
            
        if (!$this->attendanceRecord) {
            $this->attendanceRecord = new AttendanceRecord([
                'company_id' => $this->schedule->company_id,
                'user_id' => $this->user->id,
                'program_id' => $this->program->id,
                'schedule_id' => $this->schedule->id,
                'attendance_date' => $today,
                'status' => 'pending',
                'is_payable' => false
            ]);
        }
        
        Log::debug('Loaded attendance record', [
            'attendance_id' => $this->attendanceRecord->id ?? 'new',
            'status' => $this->attendanceRecord->status,
            'check_in_time' => $this->attendanceRecord->check_in_time
        ]);
    }
    
    public function selectSchedule($scheduleId)
    {
        $this->scheduleId = $scheduleId;
        $this->loadSchedule();
        $this->resetForm();
    }
    
    protected function resetForm()
    {
        $this->qrCode = '';
        $this->deviceId = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->locationName = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->checkInCompleted = false;
        $this->isCheckingIn = false;
    }
    
    public function toggleQrInput()
    {
        $this->showQrInput = !$this->showQrInput;
        if (!$this->showQrInput) {
            $this->qrCode = '';
        }
    }
    
    public function toggleLocationInput()
    {
        $this->showLocationInput = !$this->showLocationInput;
        if (!$this->showLocationInput) {
            $this->latitude = '';
            $this->longitude = '';
            $this->locationName = '';
        }
    }
    
    public function checkIn()
    {
        $this->isCheckingIn = true;
        $this->errorMessage = '';
        $this->successMessage = '';
        
        try {
            DB::transaction(function () {
                $this->validateCheckIn();
                $this->processCheckIn();
            });
            
            $this->successMessage = 'Successfully checked in!';
            $this->checkInCompleted = true;
            
        } catch (\Exception $e) {
            Log::error('Check-in failed', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id,
                'schedule_id' => $this->schedule->id
            ]);
            
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isCheckingIn = false;
        }
    }
    
    protected function validateCheckIn()
    {
        // Check if already checked in today
        if ($this->attendanceRecord->check_in_time) {
            throw new \Exception('You have already checked in for this session.');
        }
        
        // Validate schedule is still open for check-in
        if (!$this->schedule->isCheckInOpen()) {
            throw new \Exception('Check-in window has closed for this session.');
        }
        
        // Validate device if provided
        if ($this->deviceId) {
            $device = Device::find($this->deviceId);
            if (!$device || !$this->attendanceRecord->canUseDevice($device)) {
                throw new \Exception('Invalid or unauthorized device selected.');
            }
        }
        
        // Validate QR code if provided
        if ($this->qrCode && $this->schedule->qr_code_content) {
            if ($this->qrCode !== $this->schedule->qr_code_content) {
                throw new \Exception('Invalid QR code provided.');
            }
        }
        
        // Validate location if required
        if ($this->schedule->requiresLocationValidation()) {
            if (!$this->latitude || !$this->longitude) {
                throw new \Exception('Location is required for this session.');
            }
            
            if (!$this->schedule->validateLocation((float)$this->latitude, (float)$this->longitude)) {
                throw new \Exception('You are not within the required location for check-in.');
            }
        }
    }
    
    protected function processCheckIn()
    {
        // Save attendance record first
        $this->attendanceRecord->save();
        
        // Get device if specified
        $device = $this->deviceId ? Device::find($this->deviceId) : null;
        
        // Prepare location data
        $location = null;
        if ($this->latitude && $this->longitude) {
            $location = [
                'latitude' => (float)$this->latitude,
                'longitude' => (float)$this->longitude,
                'name' => $this->locationName
            ];
        }
        
        // Perform check-in
        if ($device) {
            $this->attendanceRecord->checkIn($device, $location, $this->qrCode);
        } else {
            // Manual check-in without device
            $this->attendanceRecord->update([
                'check_in_time' => now(),
                'check_in_qr_code' => $this->qrCode,
                'check_in_latitude' => $location['latitude'] ?? null,
                'check_in_longitude' => $location['longitude'] ?? null,
                'check_in_location_name' => $location['name'] ?? null,
                'check_in_ip_address' => request()->ip(),
                'status' => 'present',
                'is_payable' => true
            ]);
        }
        
        Log::info('Check-in completed', [
            'user_id' => $this->user->id,
            'schedule_id' => $this->schedule->id,
            'attendance_id' => $this->attendanceRecord->id,
            'check_in_time' => $this->attendanceRecord->check_in_time,
            'device_used' => $device?->id
        ]);
    }
    
    public function getAvailableSchedulesProperty()
    {
        $today = now();
        
        return Schedule::with(['program'])
            ->where('session_date', $today->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->where('qr_code_active', true)
            ->whereTime('check_in_opens_at', '<=', $today->format('H:i:s'))
            ->whereTime('check_in_closes_at', '>=', $today->format('H:i:s'))
            ->get();
    }
    
    public function getTodaysSchedulesProperty()
    {
        return Schedule::with(['program', 'attendanceRecords' => function($query) {
                $query->where('user_id', $this->user->id)
                      ->where('attendance_date', now()->format('Y-m-d'));
            }])
            ->where('session_date', now()->format('Y-m-d'))
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->get()
            ->map(function ($schedule) {
                $attendance = $schedule->attendanceRecords->first();
                $schedule->user_attendance_status = $attendance?->status ?? 'not_checked_in';
                $schedule->user_check_in_time = $attendance?->check_in_time;
                return $schedule;
            });
    }
    
    public function render()
    {
        return view('livewire.check-in-form', [
            'availableSchedules' => $this->availableSchedules,
            'todaysSchedules' => $this->todaysSchedules
        ]);
    }
}