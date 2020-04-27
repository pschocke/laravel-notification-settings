<?php


namespace pschocke\NotificationSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NotificationSetting extends Model
{
    use Notifiable;

    protected $guarded = [];

    public $timestamps = [
        'verified_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'meta' => 'array',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function routeVia()
    {
        return $this->type ? config('notificationSettings.handler')[$this->type]::via : '';
    }

    public function enable()
    {
        $this->update([
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function verify(int $token)
    {
        return $this->verification_token === $token;
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  \Illuminate\Notifications\Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        $correctHandler = collect(config('notificationSettings.handler'))
            ->first(function ($handler) use ($driver) {
                return (new $handler)->canSend($driver);
            });

        if ($correctHandler) {
            return (new $correctHandler)->getSend($this);
        }
    }
}
