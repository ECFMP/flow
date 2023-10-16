<?php

namespace App\Discord\FlowMeasure\Generator;

use App\Discord\FlowMeasure\Helper\EcfmpNotificationReissuer;
use App\Discord\FlowMeasure\Provider\PendingEcfmpMessage;
use App\Discord\FlowMeasure\Sender\EcfmpFlowMeasureSender;
use App\Repository\FlowMeasureNotification\RepositoryInterface;

class EcfmpFlowMeasureMessageGenerator
{
    private readonly EcfmpFlowMeasureSender $sender;

    /** @var RepositoryInterface[] */
    private readonly array $repositories;

    public function __construct(EcfmpFlowMeasureSender $sender, array $repositories)
    {
        $this->sender = $sender;
        $this->repositories = $repositories;
    }

    public function generateAndSend(): void
    {
        foreach ($this->repositories as $repository) {
            foreach ($repository->flowMeasuresToBeSentToEcfmp() as $measure) {
                $pendingMessage = new PendingEcfmpMessage(
                    $measure->measure,
                    $repository->notificationType(),
                    new EcfmpNotificationReissuer($measure, $repository->notificationType())
                );

                $this->sender->send($pendingMessage);
            }
        }
    }
}
