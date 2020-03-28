# Laravel Notification Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pschocke/laravel-notification-settings.svg?style=flat-square)](https://packagist.org/packages/pschocke/laravel-notification-settings)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/pschocke/laravel-notification-settings/run-tests?label=tests)](https://github.com/pschocke/laravel-notification-settings/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/pschocke/laravel-notification-settings.svg?style=flat-square)](https://packagist.org/packages/pschocke/laravel-notification-settings)

This package tries to extend the default laravel notification system by giving models the ability to set individual notification settings for different ways of contact.

```php
$model->saveNotificationSettingFromRequest($request);

$model->sendNotification(new OrderReceivedNotification($order); // this model will receive the notification on all channels he has configured
```

## Installation

You can install the package via composer:

```bash
composer require pschocke/laravel-notification-settings
```

Publish and run the migration:
```bash
php artisan vendor:publish --provider="pschocke\NotificationSettings\NotificationSettingsServiceProvider" --tag="migrations"
php artisan migrate
```

Publish the config file:
```bash
php artisan vendor:publish --provider="pschocke\NotificationSettings\NotificationSettingsServiceProvider" --tag="config"
```


## Usage

This package is driver based. By default, it can only send mail notifications. However, adding driver is a breeze.

To configure the package, you first need to publish the configuration file. It looks like this: 
```php
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
```

If you want to configure different settings for different models, just add a setting in the array:

```php
    'settings' => [
        App\Team::class => [
            'default' => [
                'mySetting' => 'description',
            ]
        ]
        'default' => [
            'default' => [
                'setting1' => 'description1',
                'setting2' => 'description2',
            ],
        ],
    ],

```
If you want to have different settings for different notification channels for a model, simply add an array with the notification key and the settings:

```php
    'settings' => [
        App\Team::class => [
            'default' => [
                'mySetting' => 'description',
            ]
            'mail' => [
                'mailSetting' => 'mailDescription',
            ]
        ]
        'default' => [
            'default' => [
                'setting1' => 'description1',
                'setting2' => 'description2',
            ],
        ],
    ],

```

#### Prepare your model

All models that should have notification settings need to use the `pschocke\NotificationSettings\HasNotificationSettings` trait.

#### Add a new NotificationSetting to a model

To add a notification Channel to a model, just call the `saveNotificationSetting()` method on that model with the array of settings:

```php
$myModel->saveNotificationSetting([
  'type' => 'mail',                 // the key of the handler
  'meta' => [                       // everything configured in the handler as required
      'email' => 'test@test.com',
  ],
  'settings' => [                   // the settings choices of the user
      'onNewLogin' => true,
      'onNewLogout' => false,
  ],
]);
```

#### Sending notifications

To send a notification to a model, just call the `sendNotification()` method on the model. The notification will be send through all channels that have the setting on true:

```php
$myModel->sendNotification(new OrderShippedNotification($notification), 'orderShipped'); // the second parameter is the setting name.
```


#### Write your own notification Handler

Just sending notifications via email might be enough, but some times you want to send notifications via other channels too. Luckily, adding more handler is a breeze.
Just create a new handler that implements the `pschocke\NotificationSettings\Handler\HandlerInterface` and extends the `pschocke\NotificationSettings\Handler` base class.

Just take a look at this handler that sends slack notifications:

```php
namspace App\NotificationSettingHandler;

use pschocke\NotificationSettings\Models\NotificationSetting;
use pschocke\NotificationSettings\Handler\Handler;
use pschocke\NotificationSettings\Handler\HandlerInterface;

class SlackHandler extends Handler implements HandlerInterface
{

    protected $request = [
        'webhook' => ['required', 'url'],
    ]; // the request that needs to pass in order to save the model and enabled routing

    const via = 'slack'; // the channel name that is used in the notifications via method

    protected $notificationChannel = 'slack'; // the key that is used in the config file

    public function canSend(string $methodName): bool
    {
        return $methodName === 'routeNotificationForSlack';  // the method name to route the notification
    }

    public function getSend(NotificationSetting $notificationSetting) // returns what is needed to route the notification
    {
        return $notificationSetting->meta['webhook'];
    }
}
```

After adding the handler you just need to add it to the handlers array in your config file:

```php
'handler' => [
    'mail' => pschocke\NotificationSettings\Handler\MailHandler::class,
    'slack' => App\NotificationSettingHandler\SlackHandler::class,
],
```

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email patrick@ausbildung-ms.de instead of using the issue tracker.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
