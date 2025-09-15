<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory as Faker;

trait RealisticDataSeederHelpers
{
    private function generateSouthAfricanIdNumber()
    {
        // Generate a realistic SA ID number (YYMMDDGGGGZZZ)
        $year = rand(85, 99); // 1985-1999
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $gender = rand(0, 9);
        $citizenship = rand(0, 1);
        $sequence = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return $year . $month . $day . $gender . $citizenship . $sequence;
    }

    private function getRealisticAttendanceStatus()
    {
        $statuses = [
            'present' => 70,      // 70% present
            'late' => 10,         // 10% late
            'absent_unauthorized' => 5,  // 5% absent unauthorized
            'absent_authorized' => 5,    // 5% absent authorized
            'excused' => 3,       // 3% excused
            'on_leave' => 4,      // 4% on leave
            'sick' => 3,          // 3% sick
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'present';
    }

    private function getCheckInTime($schedule, $status)
    {
        $startTime = Carbon::parse($schedule->start_time);

        if ($status === 'late') {
            // Late by 5-30 minutes
            $minutesLate = rand(5, 30);
            return $startTime->addMinutes($minutesLate)->format('H:i:s');
        }

        // On time or 1-5 minutes early
        $minutesEarly = rand(0, 5);
        return $startTime->subMinutes($minutesEarly)->format('H:i:s');
    }

    private function getCheckOutTime($schedule)
    {
        $endTime = Carbon::parse($schedule->end_time);
        // Leave 0-15 minutes early or stay 0-10 minutes late
        $minutesVariation = rand(-15, 10);
        return $endTime->addMinutes($minutesVariation)->format('H:i:s');
    }

    private function calculateHoursWorked($checkIn, $checkOut, $schedule)
    {
        if (!$checkIn || !$checkOut) return 0;

        $checkInTime = Carbon::parse($checkIn);
        $checkOutTime = Carbon::parse($checkOut);

        $hours = $checkInTime->diffInMinutes($checkOutTime) / 60;

        // Subtract break time
        $breakTime = 0.25; // 15 minutes
        $hours -= $breakTime;

        return max(0, round($hours, 2));
    }

    private function getAttendanceNotes($status)
    {
        $notes = [
            'present' => 'Attended full session',
            'late' => 'Arrived late due to transport issues',
            'absent_unauthorized' => 'No show - no explanation provided',
            'absent_authorized' => 'Absent with valid reason',
            'excused' => 'Excused absence',
            'on_leave' => 'On approved leave',
            'sick' => 'Sick leave with medical certificate',
        ];

        return $notes[$status] ?? '';
    }

    private function getLeaveReason($leaveType)
    {
        $reasons = [
            'annual' => 'Annual leave for personal matters',
            'sick' => 'Sick leave - medical appointment',
            'family_responsibility' => 'Family responsibility leave - child care',
        ];

        return $reasons[$leaveType] ?? 'Leave request';
    }

    private function getLeaveNotes($leaveType)
    {
        $notes = [
            'annual' => 'Personal matters requiring time off',
            'sick' => 'Medical appointment scheduled',
            'family_responsibility' => 'Child care responsibilities',
        ];

        return $notes[$leaveType] ?? '';
    }
}



