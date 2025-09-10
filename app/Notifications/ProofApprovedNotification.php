<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProofApprovedNotification extends Notification
{
    use Queueable;

    protected $attendanceRecord;

    /**
     * Create a new notification instance.
     */
    public function __construct(AttendanceRecord $attendanceRecord)
    {
        $this->attendanceRecord = $attendanceRecord;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proof of Absence Approved')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your proof of absence for ' . $this->attendanceRecord->date->format('M d, Y') . ' has been approved.')
            ->line('Your absence status has been updated to "Authorized" and will be included in your payment calculation.')
            ->line('Program: ' . $this->attendanceRecord->program->title)
            ->line('Date: ' . $this->attendanceRecord->date->format('M d, Y'))
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->attendanceRecord->status)))
            ->action('View Attendance Record', route('attendance.show', $this->attendanceRecord))
            ->line('Thank you for providing the necessary documentation!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'attendance_record_id' => $this->attendanceRecord->id,
            'date' => $this->attendanceRecord->date->format('Y-m-d'),
            'program' => $this->attendanceRecord->program->title,
            'status' => $this->attendanceRecord->status,
            'message' => 'Your proof of absence has been approved.',
        ];
    }
}
