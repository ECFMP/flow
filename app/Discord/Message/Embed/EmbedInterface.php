<?php

namespace App\Discord\Message\Embed;

use Ecfmp_discord\DiscordEmbeds;

interface EmbedInterface
{
    /**
     * Converts the embed to array.
     */
    public function toArray(): array;

    /**
     * Converts the embed to protobuf format.
     */
    public function toProtobuf(): DiscordEmbeds;
}
