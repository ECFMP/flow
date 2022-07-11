<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\EndTime;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Tests\TestCase;

class EndTimeTest extends TestCase
{
    public function testItHasAName()
    {
        $this->assertEquals(
            'End Time',
            (new EndTime(FlowMeasure::factory()->make()))->name()
        );
    }

    /**
     * @dataProvider endTimeProvider
     */
    public function testItReturnsCorrectEndTime(string $startTime, string $endTime, string $expected)
    {
        $measure = FlowMeasure::factory()->make();
        $measure->start_time = Carbon::parse($startTime);
        $measure->end_time = Carbon::parse($endTime);

        $this->assertEquals(
            $expected,
            (new EndTime($measure))->value()
        );
    }

    private function endTimeProvider(): array
    {
        return [
            'Same day as start time, long everything' => [
                '2022-11-22T15:26:00Z',
                '2022-11-22T16:43:00Z',
                '1643Z',
            ],
            'Same day as start time, short month' => [
                '2022-05-22T15:26:00Z',
                '2022-05-22T16:43:00Z',
                '1643Z',
            ],
            'Same day as start time, short hour' => [
                '2022-11-22T08:26:00Z',
                '2022-11-22T09:43:00Z',
                '0943Z',
            ],
            'Same day as start time, short minute' => [
                '2022-11-22T08:26:00Z',
                '2022-11-22T11:03:00Z',
                '1103Z',
            ],
            'Different day, all long' => [
                '2022-11-22T15:26:00Z',
                '2022-11-23T16:43:00Z',
                '23/11 1643Z',
            ],
            'Different day, month short' => [
                '2022-05-22T15:26:00Z',
                '2022-05-23T16:43:00Z',
                '23/05 1643Z',
            ],
            'Different day, day short' => [
                '2022-11-04T15:26:00Z',
                '2022-11-05T16:43:00Z',
                '05/11 1643Z',
            ],
            'Different day, hour short' => [
                '2022-11-22T06:26:00Z',
                '2022-11-23T07:43:00Z',
                '23/11 0743Z',
            ],
            'Different day, minute short' => [
                '2022-11-22T15:05:00Z',
                '2022-11-23T16:19:00Z',
                '23/11 1619Z',
            ],
        ];
    }
}
