<?php


namespace pschocke\NotificationSettings;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use pschocke\NotificationSettings\Handler\Handler;
use pschocke\NotificationSettings\Jobs\SendNotificationJob;
use pschocke\NotificationSettings\Models\NotificationSetting;

trait HasNotificationSettings
{
    public function notificationSettings()
    {
        return $this->morphMany(NotificationSetting::class, 'notifiable');
    }

    public function saveNotificationSettingFromRequest(Request $request)
    {
        return $this->saveNotificationSetting($request->all());
    }

    public function forceSaveNotificationSetting(array $request)
    {
        return $this->saveNotificationSetting($request, true);
    }

    public function saveNotificationSetting(array $request, bool $force = false)
    {
        $type = Str::lower($request['type']);

        if (! array_key_exists($type, config('notificationSettings.handler'))) {
            return false;
        }

        $handler = config('notificationSettings.handler')[$type];

        /** @var Handler $handler */
        $handler = (new $handler());

        $handler->forNotifiable($this);

        if (! $notificationSetting = $handler->create($request, $force)) {
            return false;
        }

        return $notificationSetting;
    }

    public function sendNotification(Notification $notification, string $notificationSettingName)
    {
        SendNotificationJob::dispatch($notification, $this, $notificationSettingName)
                                    ->onConnection(config('notificationSettings.connectionName'))
                                    ->onQueue(config('notificationSettings.queueName'));
    }
}
