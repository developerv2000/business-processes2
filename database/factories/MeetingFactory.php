<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manufacturer_id' => Manufacturer::inRandomOrder()->first()->id,
            'year' => fake()->year(),
            'who_met' => fake()->name(),
            'plan' => fake()->sentences(2, true),
            'topic' => fake()->sentences(2, true),
            'result' => fake()->sentences(2, true),
            'outside_the_exhibition' => fake()->sentences(2, true),
        ];
    }
}
