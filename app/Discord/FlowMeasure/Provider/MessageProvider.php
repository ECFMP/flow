<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Webhook\MapperInterface;
use App\Repository\FlowMeasureNotification\RepositoryInterface;
use Illuminate\Support\Collection;

class MessageProvider implements MessageProviderInterface
{
    private readonly RepositoryInterface $repository;
    private readonly MapperInterface $webhookMapper;

    public function __construct(RepositoryInterface $repository, MapperInterface $webhookMapper)
    {
        $this->repository = $repository;
        $this->webhookMapper = $webhookMapper;
    }

    public function pendingMessages(): Collection
    {
        return tap(
            new Collection(),
            function (Collection $messages) {
                foreach ($this->repository->flowMeasuresForNotification() as $flowMeasure) {
                    foreach ($this->webhookMapper->mapToWebhooks($flowMeasure) as $webhook) {
                        $messages->push(
                            new PendingDiscordMessage(
                                $flowMeasure,
                                $this->repository->notificationType(),
                                $webhook,
                                true
                            )
                        );
                    }
                }
            }
        );
    }
}
