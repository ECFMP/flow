<?php

namespace Tests\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Message\FlowMeasureMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Message\MessageGenerator;
use App\Discord\FlowMeasure\Provider\MessageProviderInterface;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use Mockery;
use Tests\TestCase;

class MessageGeneratorTest extends TestCase
{
    public function testItGeneratesMessages()
    {
        $provider = Mockery::mock(MessageProviderInterface::class);
        $measureFactory = Mockery::mock(FlowMeasureMessageFactory::class);
        $message1 = Mockery::mock(PendingMessageInterface::class);
        $message2 = Mockery::mock(PendingMessageInterface::class);
        $flowMeasureMessage1 = Mockery::mock(FlowMeasureMessage::class);
        $flowMeasureMessage2 = Mockery::mock(FlowMeasureMessage::class);
        
        $provider->expects('pendingMessages')->andReturn(collect([$message1, $message2]));
        $measureFactory->expects('make')->with($message1)->once()->andReturn($flowMeasureMessage1);
        $measureFactory->expects('make')->with($message2)->once()->andReturn($flowMeasureMessage2);

        $this->assertEquals(
            collect([$flowMeasureMessage1, $flowMeasureMessage2]),
            (new MessageGenerator($provider, $measureFactory))->generate()
        );
    }
}
