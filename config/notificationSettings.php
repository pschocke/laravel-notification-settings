<?php

return [
    /**
     * The Settings the user can choose if he wants to be notified at a given event
     */
    'settings' => [

        /**
         * The default model. Add more if you want different settings for different models
         */
        'default' => [

            /**
             * The default events for this model. Add a new array with the notitication key to have different settings for different notification channels
             */
            'default' => [
                'event1' => 'description1',
                'event2' => 'description2',
            ],
        ],
    ],

    'connectionName' => config('queue.default'),
    'queueName' => 'notifications',

    'verificationNotification' => pschocke\NotificationSettings\Notifications\NotificationSettingVerificationNotification::class,

    'handler' => [
        'mail' => pschocke\NotificationSettings\Handler\MailHandler::class,
    ],

    'driver' => [
        'mail' => [
            'verification' => [
                'enabled' => true,
                'method' => 'link',
            ],
        ],
    ],
];
