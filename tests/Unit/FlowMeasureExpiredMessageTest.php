<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Message\FlowMeasureExpiredMessage;
use App\Discord\Message\Content\ContentInterface;
use Tests\TestCase;

class FlowMeasureExpiredMessageTest extends TestCase
{
    private function getContent(): ContentInterface
    {
        return new class implements ContentInterface {
            public function toString(): string
            {
                return 'ohai';
            }
        };
    }

    public function testItReturnsMessage()
    {
        $this->assertEquals(
            "Flow Measure Expired: \n\nohai",
            (new FlowMeasureExpiredMessage($this->getContent()))->content()
        );
    }
}
