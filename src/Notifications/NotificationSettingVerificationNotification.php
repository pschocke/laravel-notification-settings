<?php


namespace pschocke\NotificationSettings\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationSettingVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$notifiable->via];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = 'https://google.com';

        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Verify your e-mail to receive e-mail notifications')
            ->action('Verify', $url)
            ->line('Thank you for using our application!');
    }
}
