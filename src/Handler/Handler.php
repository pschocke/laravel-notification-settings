<?php

namespace pschocke\NotificationSettings\Handler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use pschocke\NotificationSettings\Models\NotificationSetting;

abstract class Handler
{
    protected $notifiable;

    public function forNotifiable(Model $model)
    {
        $this->notifiable = $model;
    }

    public function create(array $request)
    {
        if (! $validated = $this->validateRequest($request)) {
            return false;
        }

        $arr = [
            'meta' => $validated,
            'type' => $this->notificationChannel,
            'settings' => $this->checkSettings($request['settings']),
        ];

        if (config('notificationSettings.driver.' . $this->notificationChannel . '.verification.enabled')) {

            $arr['verification_token'] = rand(111111, 999999);

        } else {

            $arr['verified_at'] = now();

        }

        /** @var NotificationSetting $notificationSetting */
        $notificationSetting = $this->notifiable->notificationSettings()->create($arr);

        if (config('notificationSettings.driver.' . $this->notificationChannel . '.verification.enabled')) {

            $verificationNotification = config('notificationSettings.verificationNotification');

            $notificationSetting->notify(new $verificationNotification());
        }

        return $notificationSetting;
    }



    private function validateRequest($request)
    {
        $validator = Validator::make($request['meta'], $this->request);

        return $validator->fails() ? false : $validator->validated();
    }


    private function checkSettings(array $requestedSettings)
    {
        $settings = $this->getSettingsFromClass();

        $settings = $this->getSettingsFromNotificationChannel($settings);


        $trueSettings = [];

        foreach ($settings as $setting) {
            $trueSettings[$setting] = array_key_exists($setting, $requestedSettings) && (boolval($requestedSettings[$setting]) || $requestedSettings[$setting] === 'on');
        }

        return $trueSettings;
    }

    private function getSettingsFromClass()
    {
        $className = get_class($this->notifiable);

        if (array_key_exists($className, config('notificationSettings.settings'))) {

            return config('notificationSettings.settings.' . $className);

        }

        return config('notificationSettings.settings.default');
    }

    private function getSettingsFromNotificationChannel($settings)
    {
        if (array_key_exists($this->notificationChannel, $settings)) {

            return array_keys($settings[$this->notificationChannel]);

        }

        return array_keys($settings['default']);
    }
}
