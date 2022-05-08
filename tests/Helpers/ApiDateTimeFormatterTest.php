<?php

namespace Tests\Helpers;

use App\Helpers\ApiDateTimeFormatter;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ApiDateTimeFormatterTest extends TestCase
{
    public function testItReturnsFormattedString()
    {
        $this->assertEquals(
            '2022-04-29T17:43:00Z',
            ApiDateTimeFormatter::formatDateTime(Carbon::parse('2022-04-29 17:43:00'))
        );
    }
}
