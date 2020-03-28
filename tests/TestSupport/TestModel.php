<?php

namespace pschocke\NotificationSettings\Tests\TestSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use pschocke\NotificationSettings\HasNotificationSettings;

class TestModel extends Model
{
    use HasNotificationSettings, Notifiable;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
