<?php

namespace Database\Factories;

use App\Models\Airport;
use App\Models\VatsimPilotStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VatsimPilotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cid' => $this->faker->unique()->numberBetween(900000, 1600000),
            'callsign' => $this->faker->unique()->word(),
            'altitude' => 123,
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
            'estimated_arrival_time' => Carbon::now()->addMinutes(30),
            'distance_to_destination' => $this->faker->unique()
                ->randomFloat(
                    min: 500,
                    max: 1500
                )
        ];
    }

    public function distanceToDestination(float $distance): static
    {
        return $this->state(fn () => [
            'distance_to_destination' => $distance,
        ]);
    }

    public function destination(string|Airport $airport): static
    {
        return $this->state(fn () => [
            'destination_airport' => $airport instanceof Airport ? $airport->icao_code : $airport,
        ]);
    }

    public function landedMinutesAgo(int $minutes): static
    {
        return $this->withStatus(VatsimPilotStatus::Landed)
            ->withEstimatedArrivalTime(Carbon::now()->subMinutes($minutes));
    }

    public function withNoEstimatedArrivalTime(): static
    {
        return $this->state(fn () => [
            'estimated_arrival_time' => null,
        ]);
    }

    public function withEstimatedArrivalTime(Carbon $time): static
    {
        return $this->state(fn () => [
            'estimated_arrival_time' => $time,
        ]);
    }

    public function cruising(): static
    {
        return $this->withStatus(VatsimPilotStatus::Cruise);
    }

    public function departing(): static
    {
        return $this->withStatus(VatsimPilotStatus::Departing);
    }

    public function descending(): static
    {
        return $this->withStatus(VatsimPilotStatus::Descending);
    }

    public function onTheGround(): static
    {
        return $this->withStatus(VatsimPilotStatus::Ground)
            ->withNoEstimatedArrivalTime();
    }

    public function landed(): static
    {
        return $this->landedMinutesAgo(0);
    }

    private function withStatus(VatsimPilotStatus $status): static
    {
        return $this->state(fn () => [
            'vatsim_pilot_status_id' => $status,
        ]);
    }
}
