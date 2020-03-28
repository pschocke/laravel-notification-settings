<?php


namespace pschocke\NotificationSettings\Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use pschocke\NotificationSettings\NotificationSettingsServiceProvider;
use pschocke\NotificationSettings\Tests\TestSupport\TestModel;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [NotificationSettingsServiceProvider::class];
    }

    /**
     * @var TestModel
     */
    protected $testModel;

    /**
     * @var TestModel
     */
    protected $testModelWithOneNotificationSetting;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('notificationSettings.settings.default.default', [
            'onNewLogin' => 'notify me when I log in',
            'onNewLogout' => 'notify me when I logout',
        ]);

        $this->setUpDatabase($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });

        $this->testModel = TestModel::create(['name' => 'test']);
        $this->testModelWithOneNotificationSetting = TestModel::create(['name' => 'test2']);

        include_once __DIR__.'/../database/migrations/create_notification_settings_table.php.stub';
        (new \CreateNotificationSettingsTable())->up();

        $this->testModelWithOneNotificationSetting->notificationSettings()->create([
            'type' => 'mail',
            'meta' => [
                'email' => 'test@test.com',
            ],
            'settings' => [
                'onNewLogin' => true,
                'onNewLogout' => false,
            ],
            'verified_at' => now(),
        ]);
    }
}
