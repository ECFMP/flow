<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\MemberNotEvent;
use App\Models\Event;
use Tests\TestCase;

class MemberNotEventTest extends TestCase
{
    private function getField(Event $event): MemberNotEvent
    {
        return new MemberNotEvent(
            [
                'type' => 'member_not_event',
                'value' => $event->id,
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Not Participating in Event',
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

    public function testItHasADeletedEvent()
    {
        $event = Event::factory()->create();
        $event->delete();

        $this->assertEquals(
            $event->name,
            $this->getField($event)->value()
        );
    }
}
