<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndStatus;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndStatusTest extends TestCase
{
    private function getTitle(FlowMeasure $measure): string
    {
        return (new IdentifierAndStatus($measure))->title();
    }

    public function testItIsWithdrawnWithinActivePeriod()
    {
        $measure = FlowMeasure::factory()->create();
        $measure->delete();
        $this->assertEquals($measure->identifier . ' - ' . 'Withdrawn', $this->getTitle($measure));
    }

    public function testItIsWithdrawnAfterExpiry()
    {
        $measure = FlowMeasure::factory()->finished()->create();
        $measure->delete();
        $this->assertEquals($measure->identifier . ' - ' . 'Withdrawn', $this->getTitle($measure));
    }

    public function testItIsApproaching()
    {
        $measure = FlowMeasure::factory()->notStarted()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Approaching', $this->getTitle($measure));
    }

    public function testItIsActive()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Active', $this->getTitle($measure));
    }

    public function testItIsExpired()
    {
        $measure = FlowMeasure::factory()->finished()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Expired', $this->getTitle($measure));
    }
}
