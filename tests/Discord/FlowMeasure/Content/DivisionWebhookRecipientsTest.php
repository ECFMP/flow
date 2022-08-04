<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\DivisionWebhookRecipients;
use App\Discord\Message\Tag\Tag;
use App\Models\DiscordTag;
use Tests\TestCase;

class DivisionWebhookRecipientsTest extends TestCase
{
    public function testItReturnsRecipients()
    {
        $this->assertEquals(
            '<@1234> <@5678>',
            (new DivisionWebhookRecipients(
                collect([new Tag(new DiscordTag(['tag' => '1234'])), new Tag(new DiscordTag(['tag' => '5678']))])
            ))->toString()
        );
    }
}
