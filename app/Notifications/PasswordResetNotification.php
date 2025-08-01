<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PasswordResetNotification extends Notification
{
    public $code;

    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
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
        try {
            Log::info('Sending password reset code to: ' . $notifiable->email, [
                'code' => $this->code,
                'time' => now()->toDateTimeString()
            ]);

            return (new MailMessage)
                ->subject('Your Password Reset Code - ' . config('app.name'))
                ->greeting('Hello ' . ($notifiable->first_name ?? 'User') . ',')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->line('Your password reset code is:')
                ->line(new \Illuminate\Support\HtmlString('<h1 style="text-align: center; font-size: 32px; letter-spacing: 5px; margin: 20px 0;">' . $this->code . '</h1>'))
                ->line('This code will expire in 30 minutes.')
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation('Regards,\n' . config('app.name'));

        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage(), [
                'email' => $notifiable->email,
                'exception' => $e->getMessage()
            ]);
            throw $e; // Re-throw to allow for retries
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Password reset notification failed after all retries', [
            'exception' => $exception->getMessage(),
            'code' => $this->code,
            'time' => now()->toDateTimeString()
        ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'email' => $notifiable->email
        ];
    }
}
