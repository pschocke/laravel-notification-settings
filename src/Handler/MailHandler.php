<?php

namespace pschocke\NotificationSettings\Handler;

use pschocke\NotificationSettings\Models\NotificationSetting;

class MailHandler extends Handler implements HandlerInterface
{
    protected $request = [
        'email' => ['required'],
    ];

    const via = 'mail';

    protected $notificationChannel = 'mail';

    public function canSend(string $methodName): bool
    {
        return $methodName === 'routeNotificationForMail';
    }

    public function getSend(NotificationSetting $notificationSetting)
    {
        return $notificationSetting->meta['email'];
    }
}
