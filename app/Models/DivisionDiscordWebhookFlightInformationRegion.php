<?php

namespace App\Models;

use App\Discord\Message\Tag\TagProviderInterface;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DivisionDiscordWebhookFlightInformationRegion extends Pivot implements TagProviderInterface
{
    public $incrementing = true;

    public $timestamps = true;

    public function rawTagString(): string
    {
        return (string)$this->tag;
    }
}
