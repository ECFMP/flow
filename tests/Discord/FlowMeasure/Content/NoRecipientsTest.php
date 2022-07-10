<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\NoRecipients;
use Tests\TestCase;

class NoRecipientsTest extends TestCase
{
    public function testItReturnsNoRecipients()
    {
        $this->assertEquals(
            '',
            (new NoRecipients())->toString()
        );
    }
}
