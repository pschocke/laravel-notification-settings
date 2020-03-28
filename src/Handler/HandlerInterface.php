<?php


namespace pschocke\NotificationSettings\Handler;

use pschocke\NotificationSettings\Models\NotificationSetting;

interface HandlerInterface
{
    public function canSend(string $methodName): bool;

    public function getSend(NotificationSetting $notificationSetting);
}
