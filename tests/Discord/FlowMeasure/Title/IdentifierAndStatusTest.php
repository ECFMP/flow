<?php

namespace Tests\Discord\FlowMeasure\Title;

use App\Discord\FlowMeasure\Title\IdentifierAndStatus;
use App\Enums\DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IdentifierAndStatusTest extends TestCase
{
    private function getTitle(FlowMeasure $measure): string
    {
        return (new IdentifierAndStatus($measure))->title();
    }

    public function testItIsWithdrawnWithinActivePeriod()
    {
        $measure = FlowMeasure::factory()->create();
        $measure->delete();
        $this->assertEquals($measure->identifier . ' - ' . 'Withdrawn', $this->getTitle($measure));
    }

    public function testItIsWithdrawnAfterExpiry()
    {
        $measure = FlowMeasure::factory()->finished()->create();
        $measure->delete();
        $this->assertEquals($measure->identifier . ' - ' . 'Withdrawn', $this->getTitle($measure));
    }

    public function testItIsNotified()
    {
        $measure = FlowMeasure::factory()->notStarted()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Notified', $this->getTitle($measure));
    }

    public function testItIsActive()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Active', $this->getTitle($measure));
    }

    public function testItIsExpired()
    {
        $measure = FlowMeasure::factory()->finished()->create();
        $this->assertEquals($measure->identifier . ' - ' . 'Expired', $this->getTitle($measure));
    }

    public function testItIsReissued()
    {
        $measure = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->identifier = $flowMeasure->identifier . '-2';
            $flowMeasure->save();
        })->create();

        $this->assertEquals($measure->identifier . ' - ' . 'Active (Reissued)', $this->getTitle($measure));
    }

    public function testItIsNotReissuedIfIdentifierSame()
    {
        $measure = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $this->assertEquals($measure->identifier . ' - ' . 'Active', $this->getTitle($measure));
    }
}
