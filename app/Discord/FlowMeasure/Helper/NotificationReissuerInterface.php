<?php

namespace App\Discord\FlowMeasure\Helper;

interface NotificationReissuerInterface
{
    public function isReissuedNotification(): bool;
}
