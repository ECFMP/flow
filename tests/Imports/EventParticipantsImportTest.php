<?php

namespace Tests\Imports;

use App\Imports\EventParticipantsImport;
use App\Models\Event;
use App\Models\EventParticipant;
use Tests\TestCase;

class EventParticipantsImportTest extends TestCase
{
    private readonly Event $event;
    private readonly EventParticipantsImport $import;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = Event::factory()->create();
        $this->import = new EventParticipantsImport($this->event, 'abc');
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItIgnoresRowsWithBadData(array $row)
    {
        $this->import->collection(collect([collect([$row])]));
        $this->assertDatabaseCount('event_participants', 0);
    }

    public function badDataProvider(): array
    {
        return [
            'CID null' => [[null, 'EGKK', 'EGLL']],
            'CID not a number' => [['abc', 'EGKK', 'EGLL']],
            'CID a float' => [[1203533.123, 'EGKK', 'EGLL']],
            'CID too low' => [[799999, 'EGKK', 'EGLL']],
            'CID too high for founder' => [[800151, 'EGKK', 'EGLL']],
            'CID too low for members' => [[809999, 'EGKK', 'EGLL']],
            'Origin too short, 1 character' => [[1203533, 'E', 'EGLL']],
            'Origin too short, 2 characters' => [[1203533, 'EG', 'EGLL']],
            'Origin too short, 3 characters' => [[1203533, 'EGG', 'EGLL']],
            'Origin too long, 5 characters' => [[1203533, 'EGGDX', 'EGLL']],
            'Destination too short, 1 character' => [[1203533, 'EGKK', 'E']],
            'Destination too short, 2 characters' => [[1203533, 'EGKK', 'EG']],
            'Destination too short, 3 characters' => [[1203533, 'EGKK', 'EGG']],
            'Origin invalid characters' => [[1203533, 'EG1F', 'EGGD']],
            'Destination invalid characters' => [[1203533, 'EGGD', 'EB44']],
        ];
    }

    public function testItAddsEventParticipants()
    {
        $rows = collect([collect([1203533, 'EGKK', 'EGLL']), collect([1203534, 'EGSS', 'LXGB'])]);
        $this->import->collection($rows);

        $this->assertDatabaseCount(
            'event_participants',
            2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => 'EGKK',
                'destination' => 'EGLL'
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => 'EGSS',
                'destination' => 'LXGB'
            ]
        );
    }

    public function testItAddsEventParticipantsCaseInsensitive()
    {
        $rows = collect([collect([1203533, 'EGKK', 'egll']), collect([1203534, 'egss', 'LXGB'])]);
        $this->import->collection($rows);

        $this->assertDatabaseCount(
            'event_participants',
            2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => 'EGKK',
                'destination' => 'EGLL'
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => 'EGSS',
                'destination' => 'LXGB'
            ]
        );
    }

    public function testItAddsEventParticipantsWithNullOriginAndDestination()
    {
        $rows = collect([collect([1203533, null, null]), collect([1203534, null, null])]);
        $this->import->collection($rows);

        $this->assertDatabaseCount(
            'event_participants',
            2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => null,
                'destination' => null,
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => null,
                'destination' => null,
            ]
        );
    }

    public function testItAddsEventParticipantsWithEmptyOriginAndDestination()
    {
        $rows = collect([collect([1203533, '', '']), collect([1203534, '', ''])]);
        $this->import->collection($rows);

        $this->assertDatabaseCount(
            'event_participants',
            2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => null,
                'destination' => null,
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => null,
                'destination' => null,
            ]
        );
    }

    public function testItAddsEventParticipantsWithNoOriginAndDestination()
    {
        $rows = collect([collect([1203533]), collect([1203534])]);
        $this->import->collection($rows);

        $this->assertDatabaseCount(
            'event_participants',
            2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => null,
                'destination' => null,
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => null,
                'destination' => null,
            ]
        );
    }

    public function testItRemovesPreviousEventParticipantsForEvent()
    {
        $otherEvent = Event::factory()->withParticipants()->create();
        $otherEventParticipants = EventParticipant::where('event_id', $otherEvent->id)->get();

        $this->event->participants()->createMany(
            [
                [
                    'cid' => 1203533,
                    'origin' => 'EGKK',
                    'destination' => 'EGCC'
                ],
                [
                    'cid' => 1203532,
                    'origin' => 'EGPH',
                    'destination' => 'EIDW'
                ]
            ]
        );
        $rows = collect([collect([1203533, 'EGKK', 'EGLL']), collect([1203534, 'EGSS', 'LXGB'])]);
        $this->import->collection($rows);

        $this->assertEquals($otherEventParticipants, EventParticipant::where('event_id', $otherEvent->id)->get());
        $this->assertDatabaseCount(
            'event_participants',
            $otherEventParticipants->count() + 2
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203533,
                'origin' => 'EGKK',
                'destination' => 'EGLL'
            ]
        );
        $this->assertDatabaseHas(
            'event_participants',
            [
                'cid' => 1203534,
                'origin' => 'EGSS',
                'destination' => 'LXGB'
            ]
        );
    }
}
