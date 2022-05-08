<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\ValidPeriod;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ValidPeriodTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    private function getContent(FlowMeasure $measure): string
    {
        return (new ValidPeriod($measure))->toString();
    }

    /**
     * @dataProvider periodDataProvider
     */
    public function testItReturnsCorrectPeriod(string $startTime, string $endTime, string $expected)
    {
        $measure = FlowMeasure::factory()->state(fn (array $attributes) => [
            'start_time' => Carbon::parse($startTime),
            'end_time' => Carbon::parse($endTime),
        ])->create();

        $this->assertEquals(
            'VALID: ' . $expected,
            $this->getContent($measure)
        );
    }

    public function periodDataProvider(): array
    {
        return [
            'Same day small day and month' => [
                '2022-05-06T18:57:53Z',
                '2022-05-06T23:00:00Z',
                '06/05 1857-2300Z'
            ],
            'Same day large day and month' => [
                '2022-12-23T18:57:53Z',
                '2022-12-23T23:00:00Z',
                '23/12 1857-2300Z'
            ],
            'Same day small time' => [
                '2022-12-23T08:01:00Z',
                '2022-12-23T09:09:00Z',
                '23/12 0801-0909Z'
            ],
            'Different day small day and month' => [
                '2022-05-06T18:57:53Z',
                '2022-05-07T23:00:00Z',
                '06/05 1857 - 07/05 2300Z'
            ],
            'Different day large day and month' => [
                '2022-12-23T18:57:53Z',
                '2022-12-24T23:00:00Z',
                '23/12 1857 - 24/12 2300Z'
            ],
            'Different day small time' => [
                '2022-12-23T08:01:00Z',
                '2022-12-24T09:09:00Z',
                '23/12 0801 - 24/12 0909Z'
            ],
        ];
    }
}
