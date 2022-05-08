<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Discord\Message\Content\ContentInterface;
use Tests\TestCase;

class FlowMeasureActivatedMessageTest extends TestCase
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
            "Flow Measure Activated: \n\nohai",
            (new FlowMeasureActivatedMessage($this->getContent()))->content()
        );
    }
}
