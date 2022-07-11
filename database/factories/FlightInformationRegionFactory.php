<?php

namespace Database\Factories;

use App\Models\FlightInformationRegion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlightInformationRegionFactory extends Factory
{
    protected $model = FlightInformationRegion::class;

    public function definition()
    {
        return [
            'identifier' => Str::upper($this->faker->unique()->lexify('????')),
            'name' => $this->faker->sentence(2),
        ];
    }
}
