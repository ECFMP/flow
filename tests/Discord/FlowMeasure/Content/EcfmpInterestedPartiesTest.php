<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\EcfmpInterestedParties;
use App\Discord\Message\Tag\Tag;
use App\Models\DiscordTag;
use Tests\TestCase;

class EcfmpInterestedPartiesTest extends TestCase
{
    public function testItReturnsEmptyIfNoInterestedParties()
    {
        $this->assertEquals(
            '',
            (new EcfmpInterestedParties(collect()))->toString()
        );
    }

    public function testItReturnsInterestedParties()
    {
        $expected = sprintf(
            "**FAO**: %s\nPlease acknowledge receipt with a :white_check_mark: reaction.",
            '<@1234> <@5678>'
        );
        $this->assertEquals(
            $expected,
            (new EcfmpInterestedParties(
                collect([new Tag(new DiscordTag(['tag' => '1234'])), new Tag(new DiscordTag(['tag' => '5678']))])
            ))->toString()
        );
    }
}
