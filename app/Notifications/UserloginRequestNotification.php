<?php

namespace App\Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserloginRequestNotification extends Notification
{
    use Queueable;

    protected $userName;

    /**
     * Create a new notification instance.
     *
     * @param LeaveRequest $leaveRequest
     * @param string $userName
     */
    public function __construct($userName)
    {
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
            ->subject("Wavy Tracker - User login " . $this->userName . ' ' . date('d-m-Y'))
            ->markdown('emails.user_login_created', [
                'userName' => $this->userName,
            ]);
    }
}
