<?php

namespace App\Support;

use App\Models\Country;
use App\Models\CountryCode;
use App\Models\Currency;
use App\Models\Inn;
use App\Models\Kvpp;
use App\Models\KvppPriority;
use App\Models\KvppStatus;
use App\Models\Manufacturer;
use App\Models\ManufacturerBlacklist;
use App\Models\ManufacturerCategory;
use App\Models\MarketingAuthorizationHolder;
use App\Models\Permission;
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

class ViewComposersDefiner
{
    public static function defineAll()
    {
        // Pagination limits
        View::composer('filters.partials.pagination-limit', function ($view) {
            $view->with([
                'paginationLimits' => Helper::DEFAULT_MODEL_PAGINATION_LIMITS,
            ]);
        });

        // ----------------------- Manufacturers -----------------------

        View::composer(['manufacturers.create', 'manufacturers.edit'], function ($view) {
            $view->with(self::getDefaultManufacturersShareData());
        });

        View::composer('filters.manufacturers', function ($view) {
            $shareData = self::getDefaultManufacturersShareData();

            $mergedData = array_merge($shareData, [
                'countryCodes' => CountryCode::getAll(),
                'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
            ]);

            $view->with($mergedData);
        });

        // ----------------------- Products -----------------------

        View::composer('products.edit', function ($view) {
            $view->with(self::getDefaultProductsShareData());
        });

        View::composer('products.create', function ($view) {
            $shareData = self::getDefaultProductsShareData();

            $mergedData = array_merge($shareData, [
                'productsDefaultClassID' => Product::getDefaultClassID(),
                'productsDefaultZonesIDs' => Product::getDefaultZoneIDs(),
            ]);

            $view->with($mergedData);
        });

        View::composer('filters.products', function ($view) {
            $shareData = self::getDefaultProductsShareData();

            $mergedData = array_merge($shareData, [
                'brands' => Product::getAllUniqueBrands(),
            ]);

            $view->with($mergedData);
        });

        // ----------------------- Kvpp -----------------------

        View::composer('kvpp.edit', function ($view) {
            $view->with(self::getDefaultKvppShareData());
        });

        View::composer('kvpp.create', function ($view) {
            $shareData = self::getDefaultKvppShareData();

            $mergedData = array_merge($shareData, [
                'kvppDefaultStatusID' => Kvpp::getDefaultStatusID(),
                'kvppDefaultPriorityID' => Kvpp::getDefaultPriorityID(),
            ]);

            $view->with($mergedData);
        });

        View::composer('filters.kvpp', function ($view) {
            $shareData = self::getDefaultKvppShareData();

            $mergedData = array_merge($shareData, [
                'inns' => Inn::getOnlyKvppInns(),
                'productForms' => ProductForm::getOnlyKvppForms(),
            ]);

            $view->with($mergedData);
        });

        // ----------------------- Processes -----------------------

        View::composer([
            'processes.create',
            'processes.edit',
            'processes.duplicate',
            'processes.partials.create-form-stage-inputs',
            'processes.partials.edit-form-stage-inputs'
        ], function ($view) {
            $shareData = self::getDefaultProcessesShareData();

            $mergedData = array_merge($shareData, [
                'statuses' => ProcessStatus::getAllFilteredByRoles(), // important
                'shelfLifes' => ProductShelfLife::getAll(),
                'currencies' => Currency::getAll(),
                'currencies' => Currency::getAll(),
            ]);

            $view->with($mergedData);
        });

        View::composer('filters.processes', function ($view) {
            $shareData = self::getDefaultProcessesShareData();

            $mergedData = array_merge($shareData, [
                'statuses' => ProcessStatus::getAll(), // important
                'generalStatuses' => ProcessGeneralStatus::getAll(),
                'generalStatusNamesForAnalysts' => ProcessGeneralStatus::getUniqueStatusNamesForAnalysts(),
                'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
                'brands' => Product::getAllUniqueBrands(),
            ]);

            $view->with($mergedData);
        });

        // ----------------------- Statistics -----------------------

        View::composer(['filters.statistics'], function ($view) {
            $view->with([
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'calendarMonths' => Helper::collectCalendarMonths(),
                'countryCodes' => CountryCode::getAll(),
                'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
            ]);
        });

        // ----------------------- Meetings -----------------------

        View::composer(['filters.meetings', 'meetings.create', 'meetings.edit'], function ($view) {
            $view->with([
                'manufacturers' => Manufacturer::getAllMinified(),
                'analystUsers' => User::getAnalystsMinified(),
                'bdmUsers' => User::getBdmsMinifed(),
                'countries' => Country::getAll(),
            ]);
        });

        // ----------------------- Users -----------------------

        View::composer(['users.create', 'users.edit'], function ($view) {
            $view->with([
                'roles' => Role::getAll(),
                'permissions' => Permission::getAll(),
                'countries' => Country::getAll(),
            ]);
        });

        // ----------------------- Plan -----------------------
        View::composer(['plan.country-codes.create', 'plan.country-codes.edit'], function ($view) {
            $view->with([
                'countryCodes' => CountryCode::getAll(),
                'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            ]);
        });

        View::composer([
            'plan.marketing-authorization-holders.index',
            'plan.marketing-authorization-holders.create',
            'plan.marketing-authorization-holders.edit'
        ], function ($view) {
            $view->with([
                'countryCodes' => CountryCode::getAll(),
                'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
                'calendarMonths' => Helper::collectCalendarMonths(),
            ]);
        });

        View::composer('plan.show', function ($view) {
            $view->with([
                'calendarMonths' => Helper::collectCalendarMonths(),
                'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
            ]);
        });
    }

    private static function getDefaultManufacturersShareData()
    {
        return [
            'analystUsers' => User::getAnalystsMinified(),
            'bdmUsers' => User::getBdmsMinifed(),
            'countries' => Country::getAll(),
            'manufacturers' => Manufacturer::getAllMinified(),
            'categories' => ManufacturerCategory::getAll(),
            'zones' => Zone::getAll(),
            'productClasses' => ProductClass::getAll(),
            'blacklists' => ManufacturerBlacklist::getAll(),
            'statusOptions' => Manufacturer::getStatusOptions(),
            'booleanOptions' => Helper::getBooleanOptionsArray(),
        ];
    }

    private static function getDefaultProductsShareData()
    {
        return [
            'manufacturers' => Manufacturer::getAllMinified(),
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
        ];
    }

    private static function getDefaultKvppShareData()
    {
        return [
            'booleanOptions' => Helper::getBooleanOptionsArray(),
            'statuses' => KvppStatus::getAll(),
            'countryCodes' => CountryCode::getAll(),
            'priorities' => KvppPriority::getAll(),
            'inns' => Inn::getAll(),
            'productForms' => ProductForm::getAllMinified(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            'portfolioManagers' => PortfolioManager::getAll(),
            'analystUsers' => User::getAnalystsMinified(),
        ];
    }

    private static function getDefaultProcessesShareData()
    {
        return [
            'countryCodes' => CountryCode::getAll(),
            'manufacturers' => Manufacturer::getAllMinified(),
            'inns' => Inn::getAll(),
            'productForms' => ProductForm::getAllMinified(),
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
