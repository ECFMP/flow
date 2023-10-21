<?php

namespace Tests\Discord\FlowMeasure\Generator;

use App\Discord\FlowMeasure\Generator\EcfmpFlowMeasureMessageGenerator;
use App\Discord\FlowMeasure\Provider\PendingEcfmpMessage;
use App\Discord\FlowMeasure\Sender\EcfmpFlowMeasureSender;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\FlowMeasureForNotification;
use App\Repository\FlowMeasureNotification\RepositoryInterface;
use Mockery;
use Tests\TestCase;

class EcfmpFlowMeasureMessageGeneratorTest extends TestCase
{
    public function testItSendsMessages()
    {
        $measure1 = FlowMeasure::factory()->create();
        $measure2 = FlowMeasure::factory()->create();
        $mockRepository1 = Mockery::mock(RepositoryInterface::class);
        $mockRepository1->shouldReceive('notificationType')->andReturn(DiscordNotificationType::FLOW_MEASURE_NOTIFIED);
        $mockRepository1->shouldReceive('flowMeasuresToBeSentToEcfmp')->once()->andReturn(collect(
            [
                new FlowMeasureForNotification($measure1, true),
                new FlowMeasureForNotification($measure2, false),
            ]
        ));

        $measure3 = FlowMeasure::factory()->create();
        $mockRepository2 = Mockery::mock(RepositoryInterface::class);
        $mockRepository2->shouldReceive('notificationType')->andReturn(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);
        $mockRepository2->shouldReceive('flowMeasuresToBeSentToEcfmp')->once()->andReturn(collect(
            [
                new FlowMeasureForNotification($measure3, true),
            ]
        ));

        $mockSender = Mockery::mock(EcfmpFlowMeasureSender::class);

        $mockSender->shouldReceive('send')->once()->with(Mockery::on(function (PendingEcfmpMessage $message) use ($measure1) {
            return $message->flowMeasure()->id === $measure1->id &&
                $message->isEcfmp() === true &&
                $message->type() === DiscordNotificationType::FLOW_MEASURE_NOTIFIED &&
                $message->reissue()->isReissuedNotification() === true;
        }));

        $mockSender->shouldReceive('send')->once()->with(Mockery::on(function (PendingEcfmpMessage $message) use ($measure2) {
            return $message->flowMeasure()->id === $measure2->id &&
                $message->isEcfmp() === true &&
                $message->type() === DiscordNotificationType::FLOW_MEASURE_NOTIFIED &&
                $message->reissue()->isReissuedNotification() === false;
        }));

        $mockSender->shouldReceive('send')->once()->with(Mockery::on(function (PendingEcfmpMessage $message) use ($measure3) {
            return $message->flowMeasure()->id === $measure3->id &&
                $message->isEcfmp() === true &&
                $message->type() === DiscordNotificationType::FLOW_MEASURE_ACTIVATED &&
                $message->reissue()->isReissuedNotification() === true;
        }));

        $generator = new EcfmpFlowMeasureMessageGenerator($mockSender, [$mockRepository1, $mockRepository2]);
        $generator->generateAndSend();
    }
}
