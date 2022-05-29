<?php

namespace Database\Factories;

use App\Enums\FlowMeasureType;
use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlowMeasureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fir = FlightInformationRegion::factory()->create();
        $startDate = Carbon::parse($this->faker->dateTimeBetween('-1 hour'));

        return [
            'identifier' => FlowMeasureIdentifierGenerator::generateIdentifier($startDate, $fir),
            'user_id' => User::factory()->create()->id,
            'flight_information_region_id' => $fir->id,
            'event_id' => null,
            'reason' => $this->faker->sentence(4),
            'type' => FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL->value,
            'value' => 120,
            'mandatory_route' => null,
            'filters' => [
                [
                    'type' => 'ADEP',
                    'value' => ['EG**'],
                ],
                [
                    'type' => 'ADES',
                    'value' => ['EHAM'],
                ],
            ],
            'start_time' => $startDate,
            'end_time' => $this->faker->dateTimeBetween('+1 hour', '+2 hour'),
        ];
    }

    public function withTimes(Carbon $startTime, Carbon $endTime): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => $this->faker->dateTimeBetween('-3 hour', 'now'),
            'end_time' => $this->faker->dateTimeBetween('-2 hour', 'now - 1 minute'),
        ]);
    }

    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => $this->faker->dateTimeBetween('now + 1 minute', 'now + 1 hour'),
            'end_time' => $this->faker->dateTimeBetween('now + 2 hour', 'now + 3 hour'),
        ]);
    }

    public function notNotified(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => $this->faker->dateTimeBetween('+25 hours', '+26 hours'),
            'end_time' => $this->faker->dateTimeBetween('+26 hours', '+27 hours'),
        ]);
    }

    public function withEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => Event::factory()->create()->id,
        ]);
    }

    public function withMandatoryRoute(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'mandatory_route',
            'value' => null,
            'mandatory_route' => ['LOGAN', 'UL612 LAKEY DCT NUGRA'],
        ]);
    }

    public function withArrivalAirports(array $airports): static
    {
        return $this->state(function (array $attributes) use ($airports) {
            $filteredFilters = array_filter($attributes['filters'], fn ($filter) => $filter['type'] !== 'ADES');
            return ['filters' => array_merge($filteredFilters, [['type' => 'ADES', 'value' => $airports]])];
        });
    }

    public function withDepartureAirports(array $airports): static
    {
        return $this->state(function (array $attributes) use ($airports) {
            $filteredFilters = array_filter($attributes['filters'], fn ($filter) => $filter['type'] !== 'ADEP');
            return ['filters' => array_merge($filteredFilters, [['type' => 'ADEP', 'value' => $airports]])];
        });
    }

    public function withAdditionalFilters(array $filters): static
    {
        return $this->state(fn (array $attributes) => [
            'filters' => array_merge($attributes['filters'], $filters),
        ]);
    }

    public function withAdditionalFilter(array $filter): static
    {
        return $this->withAdditionalFilters([$filter]);
    }

    public function withLevelAbove(int $level): static
    {
        return $this->withAdditionalFilter(
            [
                'type' => 'level_above',
                'value' => (string) $level,
            ]
        );
    }

    public function withLevelBelow(int $level): static
    {
        return $this->withAdditionalFilter(
            [
                'type' => 'level_above',
                'value' => (string) $level,
            ]
        );
    }

    public function withLevels(array $levels): static
    {
        return $this->withAdditionalFilter(
            [
                'type' => 'level',
                'value' => array_map(fn ($level) => (string) $level, $levels),
            ]
        );
    }

    public function withMemberEvent(Event $event): static
    {
        return $this->withAdditionalFilter(
            [
                'type' => 'member_event',
                'value' => [
                    'event_id' => (string) $event->id,
                    'event_api' => 'testapicode',
                    'event_vatcan' => 'testvatcancode',
                ]
            ]
        );
    }

    public function withMemberNotEvent(Event $event): static
    {
        return $this->withAdditionalFilter(
            [
                'type' => 'member_not_event',
                'value' => [
                    'event_id' => (string) $event->id,
                    'event_api' => 'testapicode',
                    'event_vatcan' => 'testvatcancode',
                ]
            ]
        );
    }

    public function withMeasure(FlowMeasureType $measureType, ?int $value): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $measureType->value,
            'value' => $value,
            'mandatory_route' => null,
        ]);
    }

    public function withValue(int $value): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $value,
        ]);
    }
}
