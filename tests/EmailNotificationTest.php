<?php


namespace pschocke\NotificationSettings\Tests;

use Illuminate\Support\Facades\Notification;
use pschocke\NotificationSettings\Tests\TestSupport\TestNotification;

class EmailNotificationTest extends TestCase
{
    protected $notificationSetting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationSetting = $this->testModel->notificationSettings()->create([
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

    /** @test */
    public function a_notification_setting_can_route_for_mail()
    {
        $this->assertEquals('test@test.com', $this->notificationSetting->routeNotificationFor('mail'));
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        Notification::fake();
        $this->notificationSetting->notify(new TestNotification());
        Notification::assertSentTo($this->notificationSetting, TestNotification::class);
    }
}
