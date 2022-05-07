<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Content\Reason;
use App\Models\FlowMeasure;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReasonFlowMeasureContentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    private function getContent(FlowMeasure $measure): Reason
    {
        return new Reason($measure);
    }

    public function testItReturnsReason()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            'DUE: ' . $measure->reason,
            $this->getContent($measure)->toString()
        );
    }

    public function testItReturnsNoReason()
    {
        $measure = FlowMeasure::factory()->create();
        $measure->reason = null;
        $this->assertEquals(
            'DUE: No reason given',
            $this->getContent($measure)->toString()
        );
    }
}
