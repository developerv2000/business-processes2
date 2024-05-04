<?php

namespace App\Providers;

use App\Models\Country;
use App\Models\CountryCode;
use App\Models\Inn;
use App\Models\Kvpp;
use App\Models\KvppPriority;
use App\Models\KvppSource;
use App\Models\KvppStatus;
use App\Models\Manufacturer;
use App\Models\ManufacturerBlacklist;
use App\Models\ManufacturerCategory;
use App\Models\MarketingAuthorizationHolder;
use App\Models\PortfolioManager;
use App\Models\ProductClass;
use App\Models\ProductForm;
use App\Models\ProductShelfLife;
use App\Models\User;
use App\Models\Zone;
use App\Support\Helper;
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
        //Pagination limits
        View::composer(['filters.partials.pagination-limit'], function ($view) {
            $view->with([
                'paginationLimits' => Helper::DEFAULT_MODEL_PAGINATION_LIMITS,
            ]);
        });

        // Manufacturers
        View::composer(['filters.manufacturers', 'manufacturers.create', 'manufacturers.edit'], function ($view) {
            $view->with([
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'countries' => Country::getAll(),
                'manufacturers' => Manufacturer::getAllPrioritizedAndMinifed(),
                'categories' => ManufacturerCategory::getAll(),
                'zones' => Zone::getAll(),
                'productClasses' => ProductClass::getAll(),
                'blacklists' => ManufacturerBlacklist::getAll(),
                'statusOptions' => Manufacturer::getStatusOptions(),
                'booleanOptions' => Helper::getBooleanOptionsArray(),
            ]);
        });

        // Products
        View::composer(['filters.products', 'products.create', 'products.edit'], function ($view) {
            $view->with([
                'manufacturers' => Manufacturer::getAllPrioritizedAndMinifed(),
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'productClasses' => ProductClass::getAll(),
                'productForms' => ProductForm::getAllMinified(),
                'shelfLifes' => ProductShelfLife::getAll(),
                'zones' => Zone::getAll(),
                'inns' => Inn::getAll(),
                'countries' => Country::getAll(),
                'manufacturerCategories' => ManufacturerCategory::getAll(),
                'booleanOptions' => Helper::getBooleanOptionsArray(),
            ]);
        });

        // KVPP
        View::composer(['kvpp.create', 'kvpp.edit'], function ($view) {
            $view->with([
                'statuses' => KvppStatus::getAll(),
                'countryCodes' => CountryCode::getAllPrioritized(),
                'priorities' => KvppPriority::getAll(),
                'sources' => KvppSource::getAll(),
                'inns' => Inn::getAll(),
                'forms' => ProductForm::getAllMinified(),
                'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
                'portfolioManagers' => PortfolioManager::getAll(),
                'analystUsers' => User::getAnalystsMinified(),
            ]);
        });

        // Inns and forms vary from create & update
        View::composer(['filters.kvpp'], function ($view) {
            $view->with([
                'statuses' => KvppStatus::getAll(),
                'countryCodes' => CountryCode::getAllPrioritized(),
                'priorities' => KvppPriority::getAll(),
                'sources' => KvppSource::getAll(),
                'inns' => Kvpp::getAllUsedInns(),
                'forms' => Kvpp::getAllUsedForms(),
                'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
                'portfolioManagers' => PortfolioManager::getAll(),
                'analystUsers' => User::getAnalystsMinified(),
            ]);
        });
    }
}
