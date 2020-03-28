<?php

namespace pschocke\NotificationSettings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $notifiable;
    public $notificationSettingName;

    public function __construct(Notification $notification, Model $notifiable, string $notificationSettingName)
    {
        $this->notification = $notification;
        $this->notifiable = $notifiable;
        $this->notificationSettingName = $notificationSettingName;
    }

    public function handle()
    {
        foreach ($this->notifiable->notificationSettings as $notificationSetting) {

            if ($notificationSetting->settings[$this->notificationSettingName] == true) {

                $notificationSetting->notify($this->notification);

            }

        }
    }
}
