<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndExpiredStatus;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndExpiredStatusTest extends TestCase
{
    public function testItIsHasATitle()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Expired',
            (new IdentifierAndExpiredStatus($measure))->title()
        );
    }
}
