<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Country;
use App\Models\ManufacturerBlacklist;
use App\Models\ManufacturerPresence;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Lottery;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manufacturer>
 */
class ManufacturerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'website' => 'https://' . fake()->domainName(),
            'about' => fake()->sentences(3, true),
            'relationship' => fake()->sentences(2, true),
            'is_active' => fake()->boolean(),
            'is_important' => fake()->boolean(),
            'bdm_user_id' => User::onlyBdms()->inRandomOrder()->first()->id,
            'analyst_user_id' => User::onlyAnalysts()->inRandomOrder()->first()->id,
            'category_id' => rand(1, 2),
            'country_id' => rand(1, Country::count()),
            'created_at' => fake()->dateTimeBetween('-10 year', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($manufacturer) {
            $manufacturer->presences()->saveMany([
                new ManufacturerPresence(['name' => fake()->country()]),
                new ManufacturerPresence(['name' => fake()->country()]),
            ]);

            Lottery::odds(1, 2)
                ->winner(function () use ($manufacturer) {
                    $manufacturer->blacklists()->attach(rand(1, ManufacturerBlacklist::count()));
                })
                ->choose();

            $manufacturer->zones()->attach(rand(1, Zone::count()));
            $manufacturer->productClasses()->attach(rand(1, 2));
            $manufacturer->productClasses()->attach(rand(3, 4));

            $manufacturer->comments()->saveMany([
                new Comment([
                    'body' => fake()->sentences(2, true),
                    'user_id' => User::onlyAnalysts()->inRandomOrder()->first()->id,
                    'created_at' => now()
                ]),

                new Comment([
                    'body' => fake()->sentences(2, true),
                    'user_id' => User::onlyAnalysts()->inRandomOrder()->first()->id,
                    'created_at' => now()
                ]),
            ]);
        });
    }
}
