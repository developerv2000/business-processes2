<?php

namespace App\Providers;

use App\Models\Country;
use App\Models\CountryCode;
use App\Models\Currency;
use App\Models\Inn;
use App\Models\KvppPriority;
use App\Models\KvppSource;
use App\Models\KvppStatus;
use App\Models\Manufacturer;
use App\Models\ManufacturerBlacklist;
use App\Models\ManufacturerCategory;
use App\Models\MarketingAuthorizationHolder;
use App\Models\PortfolioManager;
use App\Models\ProcessGeneralStatus;
use App\Models\ProcessResponsiblePerson;
use App\Models\ProcessStatus;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductForm;
use App\Models\ProductShelfLife;
use App\Models\Role;
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
                'productsDefaultClassID' => Product::getDefaultClassID(), // used only on create
                'productForms' => ProductForm::getAllPrioritizedAndMinifed(),
                'shelfLifes' => ProductShelfLife::getAll(),
                'zones' => Zone::getAll(),
                'productsDefaultZonesIDs' => Product::getDefaultZoneIDs(), // used only on create
                'inns' => Inn::getAllPrioritized(),
                'countries' => Country::getAll(),
                'manufacturerCategories' => ManufacturerCategory::getAll(),
                'booleanOptions' => Helper::getBooleanOptionsArray(),
            ]);
        });

        // KVPP
        View::composer(['kvpp.create', 'kvpp.edit'], function ($view) {
            $view->with(self::getKvppShareData());
        });

        // Kvpp filter
        View::composer(['filters.kvpp'], function ($view) {
            $shareData = self::getKvppShareData();

            $mergedData = array_merge($shareData, [
                'inns' => Inn::getOnlyKvppInns(),
                'productForms' => ProductForm::getOnlyKvppForms(),
            ]);

            $view->with($mergedData);
        });

        // Statistics
        View::composer(['filters.statistics'], function ($view) {
            $view->with([
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'calendarMonths' => Helper::collectCalendarMonths(),
                'countryCodes' => CountryCode::getAllPrioritized(),
            ]);
        });

        // Meetings
        View::composer(['filters.meetings', 'meetings.create', 'meetings.edit'], function ($view) {
            $view->with([
                'manufacturers' => Manufacturer::getAllPrioritizedAndMinifed(),
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'countries' => Country::getAll(),
            ]);
        });

        // Processes create/edit
        View::composer(['processes.create', 'processes.edit', 'processes.partials.create-form-stage-inputs', 'processes.partials.edit-form-stage-inputs'], function ($view) {
            $shareData = self::getProcessesShareData();

            $mergedData = array_merge($shareData, [
                'statuses' => ProcessStatus::getAllFilteredByRoles(), // important
                'shelfLifes' => ProductShelfLife::getAll(),
                'currencies' => Currency::getAll(),
                'currencies' => Currency::getAll(),
            ]);

            $view->with($mergedData);
        });

        // Processes filter
        View::composer('filters.processes', function ($view) {
            $shareData = self::getProcessesShareData();

            $mergedData = array_merge($shareData, [
                'statuses' => ProcessStatus::getAll(), // important
                'generalStatuses' => ProcessGeneralStatus::getAll(),
            ]);

            $view->with($mergedData);
        });

        // Users
        View::composer(['users.create', 'users.edit'], function ($view) {
            $view->with([
                'roles' => Role::getAll(),
            ]);
        });
    }

    private static function getKvppShareData()
    {
        return [
            'statuses' => KvppStatus::getAll(),
            'countryCodes' => CountryCode::getAllPrioritized(),
            'priorities' => KvppPriority::getAll(),
            'sources' => KvppSource::getAll(),
            'inns' => Inn::getAllPrioritized(),
            'productForms' => ProductForm::getAllPrioritizedAndMinifed(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            'portfolioManagers' => PortfolioManager::getAll(),
            'analystUsers' => User::getAnalystsMinified(),
        ];
    }

    private static function getProcessesShareData()
    {
        return [
            'countryCodes' => CountryCode::getAllPrioritized(),
            'manufacturers' => Manufacturer::getAllPrioritizedAndMinifed(),
            'inns' => Inn::getAllPrioritized(),
            'productForms' => ProductForm::getAllPrioritizedAndMinifed(),
            'analystUsers' => User::getAnalystsMinified(),
            'bdmUsers' => User::getBdmsMinifed(),
            'responsiblePeople' => ProcessResponsiblePerson::getAll(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            'productClasses' => ProductClass::getAll(),
            'manufacturerCategories' => ManufacturerCategory::getAll(),
            'countries' => Country::getAll(),
        ];
    }
}
