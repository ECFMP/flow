<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\StartTime;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Tests\TestCase;

class StartTimeTest extends TestCase
{
    public function testItHasAName()
    {
        $this->assertEquals(
            'Start Time',
            (new StartTime(FlowMeasure::factory()->make()))->name()
        );
    }

    /**
     * @dataProvider startTimeProvider
     */
    public function testItReturnsCorrectEndTime(string $startTime, string $expected)
    {
        $measure = FlowMeasure::factory()->make();
        $measure->start_time = Carbon::parse($startTime);

        $this->assertEquals(
            $expected,
            (new StartTime($measure))->value()
        );
    }

    private function startTimeProvider(): array
    {
        return [
            'Long everything' => [
                '2022-11-22T15:26:00Z',
                '22/11 1526Z',
            ],
            'Short Month' => [
                '2022-05-22T15:26:00Z',
                '22/05 1526Z',
            ],
            'Short day' => [
                '2022-11-06T15:26:00Z',
                '06/11 1526Z',
            ],
            'Short hour' => [
                '2022-11-22T03:26:00Z',
                '22/11 0326Z',
            ],
            'Short Minutes' => [
                '2022-11-22T15:02:00Z',
                '22/11 1502Z',
            ],
        ];
    }
}
