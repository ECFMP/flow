<?php

namespace Tests\Discord\FlowMeasure\Helper;

use App\Discord\FlowMeasure\Helper\EcfmpNotificationReissuer;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureNotification\FlowMeasureForNotification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EcfmpNotificationReissuerTest extends TestCase
{
    #[DataProvider('reissuingProvider')]
    public function testReissuing(bool $isReissued, DiscordNotificationType $type, bool $expected)
    {
        $measure = FlowMeasure::factory()->create();
        $notification = new FlowMeasureForNotification($measure, $isReissued);
        $reissuer = new EcfmpNotificationReissuer($notification, $type);

        $this->assertEquals($expected, $reissuer->isReissuedNotification());
    }

    public function reissuingProvider(): array
    {
        return [
            'is expired' => [true, DiscordNotificationType::FLOW_MEASURE_EXPIRED, false],
            'is activated but not reissued' => [false, DiscordNotificationType::FLOW_MEASURE_ACTIVATED, false],
            'is notified but not reissued' => [false, DiscordNotificationType::FLOW_MEASURE_NOTIFIED, false],
            'is withdrawn' => [true, DiscordNotificationType::FLOW_MEASURE_WITHDRAWN, false],
            'is notified and reissued' => [true, DiscordNotificationType::FLOW_MEASURE_NOTIFIED, true],
            'is activated and reissued' => [true, DiscordNotificationType::FLOW_MEASURE_ACTIVATED, true],
        ];
    }
}
