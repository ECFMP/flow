<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndNotifiedStatus;
use App\Discord\FlowMeasure\Title\IdentifierAndWithdrawnStatus;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndWithdrawnStatusTest extends TestCase
{
    public function testItIsHasATitle()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Withdrawn',
            (new IdentifierAndWithdrawnStatus($measure))->title()
        );
    }
}
