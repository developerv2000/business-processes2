<?php

namespace App\Support;

use App\Models\Application;
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
        self::definePaginationLimitComposer();
        self::defineManufacturerComposers();
        self::defineProductComposers();
        self::defineKvppComposers();
        self::defineProcessComposers();
        self::defineStatisticsComposer();
        self::defineMeetingComposers();
        self::defineUserComposers();
        self::definePlanComposers();
        self::defineApplicationComposers();
    }

    private static function definePaginationLimitComposer()
    {
        self::defineViewComposer('filters.partials.pagination-limit', [
            'paginationLimits' => Helper::DEFAULT_MODEL_PAGINATION_LIMITS,
        ]);
    }

    private static function defineManufacturerComposers()
    {
        $manufacturerData = self::getDefaultManufacturersShareData();
        self::defineViewComposer(['manufacturers.create', 'manufacturers.edit'], $manufacturerData);
        self::defineViewComposer('filters.manufacturers', array_merge($manufacturerData, [
            'countryCodes' => CountryCode::getAll(),
            'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
        ]));
    }

    private static function defineProductComposers()
    {
        $productData = self::getDefaultProductsShareData();
        self::defineViewComposer(['products.create'], array_merge($productData, [
            'productsDefaultClassID' => Product::getDefaultClassID(),
            'productsDefaultZonesIDs' => Product::getDefaultZoneIDs(),
        ]));
        self::defineViewComposer(['products.edit'], $productData);
        self::defineViewComposer('filters.products', array_merge($productData, [
            'brands' => Product::getAllUniqueBrands(),
        ]));
    }

    private static function defineKvppComposers()
    {
        $kvppData = self::getDefaultKvppShareData();
        self::defineViewComposer(['kvpp.edit', 'kvpp.create'], array_merge($kvppData, [
            'kvppDefaultStatusID' => Kvpp::getDefaultStatusID(),
            'kvppDefaultPriorityID' => Kvpp::getDefaultPriorityID(),
        ]));
        self::defineViewComposer('filters.kvpp', array_merge($kvppData, [
            'inns' => Inn::getOnlyKvppInns(),
            'productForms' => ProductForm::getOnlyKvppForms(),
        ]));
    }

    private static function defineProcessComposers()
    {
        $processData = self::getDefaultProcessesShareData();
        self::defineViewComposer([
            'processes.create',
            'processes.edit',
            'processes.duplicate',
            'processes.partials.create-form-stage-inputs',
            'processes.partials.edit-form-stage-inputs'
        ], array_merge($processData, [
            'statuses' => ProcessStatus::getAllFilteredByRoles(),
            'shelfLifes' => ProductShelfLife::getAll(),
            'currencies' => Currency::getAll(),
        ]));
        self::defineViewComposer('filters.processes', array_merge($processData, [
            'statuses' => ProcessStatus::getAll(),
            'generalStatuses' => ProcessGeneralStatus::getAll(),
            'generalStatusNamesForAnalysts' => ProcessGeneralStatus::getUniqueStatusNamesForAnalysts(),
            'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
            'brands' => Product::getAllUniqueBrands(),
        ]));
    }

    private static function defineStatisticsComposer()
    {
        self::defineViewComposer('filters.statistics', [
            'analystUsers' => User::getAnalystsMinified(),
            'bdmUsers' => User::getBdmsMinifed(),
            'calendarMonths' => Helper::collectCalendarMonths(),
            'countryCodes' => CountryCode::getAll(),
            'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
        ]);
    }

    private static function defineMeetingComposers()
    {
        self::defineViewComposer(['filters.meetings', 'meetings.create', 'meetings.edit'], [
            'manufacturers' => Manufacturer::getAllMinified(),
            'analystUsers' => User::getAnalystsMinified(),
            'bdmUsers' => User::getBdmsMinifed(),
            'countries' => Country::getAll(),
        ]);
    }

    private static function defineUserComposers()
    {
        self::defineViewComposer(['users.create', 'users.edit'], [
            'roles' => Role::getAll(),
            'permissions' => Permission::getAll(),
            'countryCodes' => CountryCode::getAll(),
        ]);
    }

    private static function definePlanComposers()
    {
        self::defineViewComposer(['plan.country-codes.create', 'plan.country-codes.edit'], [
            'countryCodes' => CountryCode::getAll(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
        ]);
        self::defineViewComposer([
            'plan.marketing-authorization-holders.index',
            'plan.marketing-authorization-holders.create',
            'plan.marketing-authorization-holders.edit'
        ], [
            'countryCodes' => CountryCode::getAll(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            'calendarMonths' => Helper::collectCalendarMonths(),
        ]);
        self::defineViewComposer('plan.show', [
            'calendarMonths' => Helper::collectCalendarMonths(),
            'specificManufacturerCountries' => Manufacturer::getSpecificCountryOptions(),
        ]);
    }

    private static function defineApplicationComposers()
    {
        self::defineViewComposer(['filters.applications'], self::getDefaultApplicationsShareData());
    }

    private static function defineViewComposer($views, array $data)
    {
        View::composer($views, function ($view) use ($data) {
            $view->with($data);
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

    private static function getDefaultApplicationsShareData()
    {
        return [
            'applications' => Application::getAllMinified(),
            'countryCodes' => CountryCode::getAll(),
            'marketingAuthorizationHolders' => MarketingAuthorizationHolder::getAll(),
            'manufacturers' => Manufacturer::getAllMinified(),
            'enTrademarks' => Application::pluckAllEnTrademarks(),
            'ruTrademarks' => Application::pluckAllRuTrademarks(),
        ];
    }
}
