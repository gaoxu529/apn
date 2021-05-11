<?php

namespace NotificationChannels\Apn\Tests;

use Illuminate\Notifications\Notifiable;

class TestNotifiable
{
    use Notifiable;

    /**
     * @return array
     */
    public function routeNotificationForApn()
    {
        return [
            '0851273d15351ca2c6d00834f13c636b00bafcf69372eb093a1ec8a78fb0bad2',
        ];
    }

    /**
     * @return array
     */
    public function routeNotificationForApnVoip()
    {
        return [
            '0851273d15351ca2c6d00834f13c636b00bafcf69372eb093a1ec8a78fb0bad2',
        ];
    }
}
