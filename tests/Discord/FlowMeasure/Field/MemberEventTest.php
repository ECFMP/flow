<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\MemberEvent;
use App\Models\Event;
use Tests\TestCase;

class MemberEventTest extends TestCase
{
    private function getField(Event $event): MemberEvent
    {
        return new MemberEvent(
            [
                'type' => 'member_event',
                'value' => $event->id,
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Participating in Event',
            $this->getField(Event::factory()->create())->name()
        );
    }

    public function testItHasAnEvent()
    {
        $event = Event::factory()->create();

        $this->assertEquals(
            $event->name,
            $this->getField($event)->value()
        );
    }
}
