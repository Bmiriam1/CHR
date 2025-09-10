<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProofRejectedNotification extends Notification
{
    use Queueable;

    protected $attendanceRecord;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(AttendanceRecord $attendanceRecord, string $reason = null)
    {
        $this->attendanceRecord = $attendanceRecord;
        $this->reason = $reason;
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
        $mailMessage = (new MailMessage)
            ->subject('Proof of Absence Rejected')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your proof of absence for ' . $this->attendanceRecord->date->format('M d, Y') . ' has been rejected.')
            ->line('Your absence status remains as "Unauthorized" and will not be included in your payment calculation.')
            ->line('Program: ' . $this->attendanceRecord->program->title)
            ->line('Date: ' . $this->attendanceRecord->date->format('M d, Y'))
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->attendanceRecord->status)));

        if ($this->reason) {
            $mailMessage->line('Reason: ' . $this->reason);
        }

        return $mailMessage
            ->action('Upload New Proof', route('attendance.upload-proof', $this->attendanceRecord))
            ->line('Please upload additional documentation or contact your supervisor for assistance.');
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
            'reason' => $this->reason,
            'message' => 'Your proof of absence has been rejected.',
        ];
    }
}
