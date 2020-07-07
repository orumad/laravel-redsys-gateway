<?php

namespace Orumad\LaravelRedsys\Events;

use Illuminate\Queue\SerializesModels;
use Orumad\LaravelRedsys\Models\RedsysNotification;

class RedsysNotificationArrived
{
    use SerializesModels;

    public RedsysNotification $notification;

    public function __construct(RedsysNotification $notification)
    {
        $this->notification = $notification;
    }

}