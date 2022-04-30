<?php

namespace Tests\Unit;

use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowMeasureIdentifierGeneratorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')
            ->delete();
        DB::table('events')
            ->delete();
        DB::table('flight_information_regions')
            ->delete();
        DB::table('users')
            ->delete();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testItGeneratesFlowMeasures(
        int $numberExisting,
        string $date,
        string $expectedDayNumber,
        string $expectedDesignator
    ) {
        $fir = FlightInformationRegion::factory()->create(['identifier' => 'EGTT']);
        $otherFir = FlightInformationRegion::factory()->create(['identifier' => 'EGPX']);

        // Create measure on the previous day
        $test = FlowMeasure::factory()->state(
            [
                'flight_information_region_id' => $fir->id,
                'start_time' => Carbon::parse($date)->subDay()->startOfDay(),
                'end_time' => Carbon::parse($date)->subDay()->endOfDay(),
            ]
        )->create();

        // Create measure on the next day
        FlowMeasure::factory()->state(
            [
                'flight_information_region_id' => $fir->id,
                'start_time' => Carbon::parse($date)->addDay()->startOfDay(),
                'end_time' => Carbon::parse($date)->addDay()->endOfDay(),
            ]
        )->create();

        // Create measure in another FIR
        FlowMeasure::factory()->state(
            [
                'flight_information_region_id' => $otherFir->id,
                'start_time' => Carbon::parse($date)->startOfDay(),
                'end_time' => Carbon::parse($date)->endOfDay(),
            ]
        )->create();

        // Create other measures today if needed
        FlowMeasure::factory()->count($numberExisting)->state(
            [
                'flight_information_region_id' => $fir->id,
                'start_time' => Carbon::parse($date)->startOfDay(),
                'end_time' => Carbon::parse($date)->endOfDay(),
            ]
        )->create();

        $this->assertEquals(
            sprintf('EGTT%s%s', $expectedDayNumber, $expectedDesignator),
            FlowMeasureIdentifierGenerator::generateIdentifier(Carbon::parse($date), $fir)
        );
    }

    public function dataProvider()
    {
        return [
            'First of the day' => [0, '2022-04-30 12:00:00', '30', 'A'],
            'Second of the day' => [1, '2022-04-30 12:00:00', '30', 'B'],
            'A lot today' => [10, '2022-04-30 12:00:00', '30', 'K'],
            'End of the letters' => [25, '2022-04-30 12:00:00', '30', 'Z'],
            'Rolling over' => [26, '2022-04-30 12:00:00', '30', 'AA'],
            'Starting again' => [27, '2022-04-30 12:00:00', '30', 'AB'],
            'Single digit number' => [0, '2022-04-01 12:00:00', '01', 'A'],
            'Single digit number high' => [0, '2022-04-09 12:00:00', '09', 'A'],
        ];
    }
}
