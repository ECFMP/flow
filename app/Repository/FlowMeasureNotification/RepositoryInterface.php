<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * Get all the flow measures for notification.
     */
    public function flowMeasuresForNotification(): Collection;

    /**
     * The type of discord notification.
     */
    public function notificationType(): DiscordNotificationType;
}
