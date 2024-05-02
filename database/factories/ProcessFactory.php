<?php

namespace Database\Factories;

use App\Models\CountryCode;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'status_id' => ProcessStatus::inRandomOrder()->first()->id,
            'status_update_date' => fake()->date(),
            'country_code_id' => CountryCode::inRandomOrder()->first()->id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($process) {
            $process->responsiblePeople()->attach(rand(1, 10));
            $process->responsiblePeople()->attach(rand(11, 20));
        });
    }
}
