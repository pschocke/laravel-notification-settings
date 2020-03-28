<?php

namespace pschocke\NotificationSettings;

use Illuminate\Support\ServiceProvider;

class NotificationSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/notificationSettings.php' => config_path('notificationSettings.php'),
            ], 'config');

            if (! class_exists('CreateNotificationSettingsTable')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_notification_settings_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_notification_settings_table.php'),
                ], 'migrations');
            }

            /*
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'skeleton');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/skeleton'),
            ], 'views');
            */
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notificationSettings.php', 'notificationSettings');
    }
}
