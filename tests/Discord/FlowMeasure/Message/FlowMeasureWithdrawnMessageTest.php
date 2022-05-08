<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Discord\Message\Content\ContentInterface;
use Tests\TestCase;

class FlowMeasureWithdrawnMessageTest extends TestCase
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
            "Flow Measure Withdrawn: \n\nohai",
            (new FlowMeasureWithdrawnMessage($this->getContent()))->content()
        );
    }
}
