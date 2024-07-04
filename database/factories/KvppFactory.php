<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CountryCode;
use App\Models\Inn;
use App\Models\KvppPriority;
use App\Models\KvppStatus;
use App\Models\MarketingAuthorizationHolder;
use App\Models\PortfolioManager;
use App\Models\ProductForm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kvpp>
 */
class KvppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status_id' => KvppStatus::inRandomOrder()->first()->id,
            'country_code_id' => CountryCode::inRandomOrder()->first()->id,
            'priority_id' => KvppPriority::inRandomOrder()->first()->id,
            'source_eu' => fake()->boolean(),
            'source_in' => fake()->boolean(),
            'inn_id' => Inn::inRandomOrder()->first()->id,
            'form_id' => ProductForm::inRandomOrder()->first()->id,
            'marketing_authorization_holder_id' => MarketingAuthorizationHolder::inRandomOrder()->first()->id,
            'dosage' => fake()->numberBetween(10, 100),
            'pack' => fake()->numberBetween(10, 100) . ' ML',
            'additional_search_information' => fake()->sentences(2, true),
            'forecast_year_1' => fake()->numberBetween(10, 5000),
            'forecast_year_2' => fake()->numberBetween(10, 5000),
            'forecast_year_3' => fake()->numberBetween(10, 5000),
            'portfolio_manager_id' => PortfolioManager::inRandomOrder()->first()->id,
            'analyst_user_id' => User::onlyAnalysts()->inRandomOrder()->first()->id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($kvpp) {
            $kvpp->additionalSearchCountries()->attach(rand(1, 10));
            $kvpp->additionalSearchCountries()->attach(rand(11, 20));

            $kvpp->comments()->saveMany([
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
