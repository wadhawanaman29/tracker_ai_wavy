<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApproveNotification extends Notification
{
    use Queueable;

    protected $leaveRequest;
    protected $userName;

    /**
     * Create a new notification instance.
     *
     * @param LeaveRequest $leaveRequest
     * @param string $userName
     */
    public function __construct(LeaveRequest $leaveRequest, $userName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Wavy Tracker - Leave Approved Successfully ' . $this->userName . ' ' . date('d-m-Y'))
            ->markdown('emails.leave_approved', [
                'leaveRequest' => $this->leaveRequest,
                'userName' => $this->userName,
            ]);
          
    }
}
