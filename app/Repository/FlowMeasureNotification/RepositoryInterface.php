<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * Returns the flow measures that need to be sent to ECFMP.
     */
    public function flowMeasuresToBeSentToEcfmp(): Collection;

    /**
     * Get all the flow measures for notification.
     */
    public function flowMeasuresForNotification(): Collection;

    /**
     * The type of discord notification.
     */
    public function notificationType(): DiscordNotificationType;
}
