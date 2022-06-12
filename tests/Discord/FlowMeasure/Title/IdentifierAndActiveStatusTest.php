<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndActiveStatus;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndActiveStatusTest extends TestCase
{
    public function testItIsNotReissued()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Active',
            IdentifierAndActiveStatus::create($measure)->title()
        );
    }

    public function testItIsReissued()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals(
            $measure->identifier . ' - Active (Reissued)',
            IdentifierAndActiveStatus::createReissued($measure)->title()
        );
    }
}
