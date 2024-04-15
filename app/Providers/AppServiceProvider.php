<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Manufacturers
        View::composer(['filters.manufacturers', 'manufacturers.create', 'manufacturers.edit'], function ($view) {
            $view->with([
                'analystUsers' => User::getAnalystsMinified(),
                // 'manufacturers' => Manufacturer::getAllMinifed(),
                // 'categories' => ManufacturerCategory::getAll(),
                // 'bdmUsers' => User::getBdmsMinifed(),
                // 'countries' => Country::getAll(),
                // 'zones' => Zone::getAll(),
                // 'productCategories' => ProductCategory::getAll(),
                // 'blacklists' => Blacklist::getAll(),
                // 'statusOptions' => Manufacturer::getStatusOptions(),
            ]);
        });
    }
}
