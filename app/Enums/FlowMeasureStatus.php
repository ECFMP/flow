<?php

namespace App\Enums;

enum FlowMeasureStatus: string
{
    case ACTIVE = 'active';
    case NOTIFIED = 'notified';
    case EXPIRED = 'expired';
    case DELETED = 'deleted';
}
