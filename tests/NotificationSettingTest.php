<?php


namespace pschocke\NotificationSettings\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use pschocke\NotificationSettings\Jobs\SendNotificationJob;
use pschocke\NotificationSettings\Notifications\NotificationSettingVerificationNotification;
use pschocke\NotificationSettings\Tests\TestSupport\TestModel;
use pschocke\NotificationSettings\Tests\TestSupport\TestNotification;

class NotificationSettingTest extends TestCase
{
    protected $request;

    protected $array;

    protected function setUp(): void
    {
        parent::setUp();
        $request = new Request();
        $this->array = [
            'type' => 'mail',
            'meta' => [
                'email' => 'test@test.com',
            ],
            'settings' => [
                'onNewLogin' => true,
                'onNewLogout' => false,
            ],
        ];
        $request->replace($this->array);
        $this->request = $request;
    }


    /** @test */
    public function it_can_be_created_from_request()
    {
        Notification::fake();
        $this->assertNotFalse($this->testModel->saveNotificationSettingFromRequest($this->request));
        $this->assertCount(1, $this->testModel->notificationSettings);
        $first = $this->testModel->notificationSettings()->first();
        Notification::assertSentTo(
            $first,
            NotificationSettingVerificationNotification::class,
        );

        $this->assertTrue($first->settings['onNewLogin']);
        $this->assertFalse($first->settings['onNewLogout']);
    }

    /** @test */
    public function it_can_set_different_settings_for_different_models()
    {
        config()->set('notificationSettings.settings.' . TestModel::class . '.default', [
            'setting1' => 'setting1',
        ]);

        $this->array['settings']['setting1'] = true;

        Notification::fake();
        $this->assertNotFalse($this->testModel->saveNotificationSetting($this->array));
        $this->assertCount(1, $this->testModel->notificationSettings);

        $first = $this->testModel->notificationSettings()->first();
        $this->assertTrue($first->settings['setting1']);
    }

    /** @test */
    public function a_model_can_get_notifications_for_a_model()
    {
        Queue::fake();

        $this->testModelWithOneNotificationSetting->sendNotification(new TestNotification(), 'onNewLogin');

        $model = $this->testModelWithOneNotificationSetting;
        Queue::assertPushed(
            SendNotificationJob::class,
            function ($job) use ($model) {
                return $job->notifiable->is($model);
            }
        );
    }

    /** @test */
    public function the_job_only_sends_notifications_when_by_user_enabled()
    {
        Notification::fake();

        $job = new SendNotificationJob(
            new TestNotification(),
            $this->testModelWithOneNotificationSetting,
            'onNewLogin'
        );
        $job->handle();

        Notification::assertSentTo(
            $this->testModelWithOneNotificationSetting->notificationSettings()->first(),
            TestNotification::class
        );

        Notification::fake();

        $job = new SendNotificationJob(
            new TestNotification(),
            $this->testModelWithOneNotificationSetting,
            'onNewLogout'
        );
        $job->handle();

        Notification::assertNotSentTo(
            $this->testModelWithOneNotificationSetting->notificationSettings()->first(),
            TestNotification::class
        );
    }
}
