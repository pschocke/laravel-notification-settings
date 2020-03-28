<?php


namespace pschocke\NotificationSettings\Tests;


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
        $this->assertEquals('test@test.com', $this->notificationSetting->routeNotificationForMail());
    }


}
