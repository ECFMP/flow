<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\DivisionDiscordNotification;
use App\Models\FlowMeasure;
use App\Repository\FlowMeasureRepository;
use Carbon\Carbon;

class ExpiredWebhookFilter implements FilterInterface
{
    use ChecksForDiscordNotificationsToWebhook;

    private readonly FlowMeasureRepository $flowMeasureRepository;

    public function __construct(FlowMeasureRepository $flowMeasureRepository)
    {
        $this->flowMeasureRepository = $flowMeasureRepository;
    }

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        // A division webhook
        if (!is_null($webhook->id())) {
            return false;
        }

        // Doesnt meet conditions
        if (
            $this->notManyWebhooksRecentlySent() &&
            $this->notRevisedMoreThanOnce($flowMeasure) &&
            $this->lessThanThreeActiveMeasures($flowMeasure)
        ) {
            return false;
        }

        return $this->existingNotificationDoesntExist(
            $flowMeasure->withdrawnAndExpiredDiscordNotifications(),
            $webhook
        );
    }

    private function notManyWebhooksRecentlySent(): bool
    {
        return DivisionDiscordNotification::where('created_at', '>=', Carbon::now()->subHours(2))
            ->whereNull('division_discord_webhook_id')
            ->count() <= 5;
    }

    private function notRevisedMoreThanOnce(FlowMeasure $flowMeasure): bool
    {
        return FlowMeasureIdentifierGenerator::timesRevised($flowMeasure) < 2;
    }

    private function lessThanThreeActiveMeasures(FlowMeasure $measure): bool
    {
        return $this->flowMeasureRepository->getFlowMeasuresActiveDuringPeriod($measure->start_time, $measure->end_time)
            ->reject(fn (FlowMeasure $activeMeasure) => $activeMeasure->id === $measure->id)
            ->count() < 3;
    }
}
