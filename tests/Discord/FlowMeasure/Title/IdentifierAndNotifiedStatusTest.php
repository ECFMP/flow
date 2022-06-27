<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndNotifiedStatus;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndNotifiedStatusTest extends TestCase
{
    public function testItIsNotReissued()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Notified',
            IdentifierAndNotifiedStatus::create($measure)->title()
        );
    }

    public function testItIsReissued()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Notified (Reissued)',
            IdentifierAndNotifiedStatus::createReissued($measure)->title()
        );
    }
}
