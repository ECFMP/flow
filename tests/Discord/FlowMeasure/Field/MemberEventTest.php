<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\MemberEvent;
use App\Models\Event;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MemberEventTest extends TestCase
{
    private function getField(Collection $events): MemberEvent
    {
        return new MemberEvent(
            [
                'type' => 'member_event',
                'value' => $events->map(fn(Event $event) => $event->id)->toArray(),
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Participating in Event',
            $this->getField(Event::factory()->count(3)->create())->name()
        );
    }

    public function testItHasAnEvent()
    {
        $event = Event::factory()->create();

        $this->assertEquals(
            $event->name,
            $this->getField(collect([$event]))->value()
        );
    }

    public function testItHasADeletedEvent()
    {
        $event = Event::factory()->create();
        $event->delete();

        $this->assertEquals(
            $event->name,
            $this->getField(collect([$event]))->value()
        );
    }

    public function testItHasMultipleEvents()
    {
        $event = Event::factory()->create();
        $event2 = Event::factory()->create();

        $this->assertEquals(
            sprintf('%s, %s', $event->name, $event2->name),
            $this->getField(collect([$event, $event2]))->value()
        );
    }
}
