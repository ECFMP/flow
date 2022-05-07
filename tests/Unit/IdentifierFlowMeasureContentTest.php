<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Content\Identifier;
use App\Models\FlowMeasure;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IdentifierFlowMeasureContentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    private function getContent(FlowMeasure $measure): Identifier
    {
        return new Identifier($measure);
    }

    public function testItReturnsJustIdentifierIfNoEvent()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier,
            $this->getContent($measure)->toString()
        );
    }

    public function testItReturnsIdentifierWithEventName()
    {
        $measure = FlowMeasure::factory()->withEvent()->create();
        $this->assertEquals(
            $measure->identifier . ' - ' . $measure->event->name,
            $this->getContent($measure)->toString()
        );
    }
}
