<?php


namespace pschocke\NotificationSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'routeNotificationFor')) {
            $correctHandler = collect(config('notificationSettings.handler'))
                ->first(function ($handler) use ($method) {
                    return (new $handler)->canSend($method);
                });

            if ($correctHandler) {
                return (new $correctHandler)->getSend($this);
            }
        }

        return parent::__call($method, $parameters);
    }
}
