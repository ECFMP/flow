<?php

namespace App\Helpers;

use Carbon\Carbon;

class ApiDateTimeFormatter
{
    public static function formatDateTime(Carbon $time): string
    {
        return $time->avoidMutation()
            ->utc()
            ->isoFormat("YYYY-MM-DD[T]HH:mm:ss[Z]");
    }
}
