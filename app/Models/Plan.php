<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use App\Support\Traits\CalculatesPlanQuarterAndYearCounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Plan extends CommentableModel
{
    use HasFactory;
    use CalculatesPlanQuarterAndYearCounts;

    const DEFAULT_ORDER_BY = 'year';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    protected $guarded = ['id'];
    public $timestamps = false;

    protected $with = [
        'countryCodes',
        'lastComment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function countryCodes()
    {
        return $this->belongsToMany(CountryCode::class);
    }

    public function marketingAuthorizationHolders()
    {
        return $this->belongsToMany(MarketingAuthorizationHolder::class, 'plan_country_code_marketing_authorization_holder')
            ->withPivot(self::getPivotColumnNamesForMAH());
    }

    /**
     * Return marketing authorization holders for specific country code
     */
    public function MAHsOfCountryCode($countryCode)
    {
        return $this->marketingAuthorizationHolders()
            ->wherePivot('country_code_id', $countryCode->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::deleting(function ($instance) {
            foreach ($instance->comments as $comment) {
                $comment->delete();
            }

            foreach ($instance->countryCodes as $countryCode) {
                $instance->detachCountryCode($countryCode);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy(self::DEFAULT_ORDER_BY, self::DEFAULT_ORDER_TYPE)
            ->withCount('comments')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

    public static function createFromRequest($request)
    {
        $instance = self::create($request->all());

        // HasMany relations
        $instance->storeComment($request->comment);
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // HasMany relations
        $this->storeComment($request->comment);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return $this->year;
    }

    public static function getByYearFromRequest($request)
    {
        return self::where('year', $request->input('year'))->firstOrFail();
    }

    /**
     * Load MAHs for each of plans country codes, to avoid redundant queries
     */
    public function loadMAHsOfCountryCodes()
    {
        foreach ($this->countryCodes as $countryCode) {
            $countryCode->marketing_authorization_holders = $countryCode->MAHsOfPlan($this)->get();
        }
    }

    /**
     * Calculate and load all plan calculations based on plans year.
     * Calculations must be done in below proper order to avoid errors:
     *
     * 1. Marketing authorization holders calculations of each country code.
     * 2. Country codes calculations.
     * 3. Plans year calculations.
     */
    public function makeAllCalculations($request)
    {
        $this->loadMAHsOfCountryCodes();

        foreach ($this->countryCodes as $countryCode) {
            // Step 1: Marketing authorization holders calculations of each country code.
            foreach ($countryCode->marketing_authorization_holders as $mah) {
                $mah->makeAllPlanCalculations($request, $this);
            }

            // Step 2: Country codes calculations.
            $countryCode->makeAllPlanCalculations($request, $this);
        }

        // Step 3: Plans summary calculations.
        $this->makeAllSummaryCalculations($request);
    }

    /**
     * Return array of the pivot column names for 'MAHsOfCountryCode' relationship
     * 'plan_country_code_marketing_authorization_holder' table
     */
    public static function getPivotColumnNamesForMAH(): array
    {
        return [
            'plan_id',
            'country_code_id',
            'marketing_authorization_holder_id',

            'January_europe_contract_plan',
            'February_europe_contract_plan',
            'March_europe_contract_plan',
            'April_europe_contract_plan',
            'May_europe_contract_plan',
            'June_europe_contract_plan',
            'July_europe_contract_plan',
            'August_europe_contract_plan',
            'September_europe_contract_plan',
            'October_europe_contract_plan',
            'November_europe_contract_plan',
            'December_europe_contract_plan',

            'January_india_contract_plan',
            'February_india_contract_plan',
            'March_india_contract_plan',
            'April_india_contract_plan',
            'May_india_contract_plan',
            'June_india_contract_plan',
            'July_india_contract_plan',
            'August_india_contract_plan',
            'September_india_contract_plan',
            'October_india_contract_plan',
            'November_india_contract_plan',
            'December_india_contract_plan',

            'January_comment',
            'February_comment',
            'March_comment',
            'April_comment',
            'May_comment',
            'June_comment',
            'July_comment',
            'August_comment',
            'September_comment',
            'October_comment',
            'November_comment',
            'December_comment',
        ];
    }

    /**
     * Used in route plan.country.codes.destroy
     * and on plans deleting event function.
     */
    public function detachCountryCode($countryCode)
    {
        $MAHs = $countryCode->MAHsOfPlan($this)->get();
        $MAHIds = $MAHs->pluck('id');

        // Detach marketing authorization holders first
        $this->detachMarketingAuthorizationHolders($countryCode, $MAHIds);

        // Then detach country code
        $this->countryCodes()->detach([$countryCode->id]);
    }

    /**
     * Used in plan.marketing.authorization.holders.destroy
     * and plan.country.codes.destroy routes
     */
    public function detachMarketingAuthorizationHolders($countryCode, $MAHIds)
    {
        foreach ($MAHIds as $mahID) {
            $this->MAHsOfCountryCode($countryCode)
                ->wherePivot('marketing_authorization_holder_id', $mahID)->detach();
        }
    }

    /**
     * Perform all plan calculations: Monthly, Quarterly, Yearly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function makeAllSummaryCalculations($request)
    {
        // Perform monthly, quarterly, and yearly calculations
        $this->calculatePlanMonthlyProcessCounts();
        $this->calculatePlanQuartersProcessCounts();
        $this->calculatePlanYearProcessCounts();
        $this->calculatePlanYearPercentages();
    }

    /**
     * Calculate the total 'contract_plan', 'contract_fact', and 'register_fact'
     * counts for each month based on related Country codes.
     *
     * This method sets the following properties for each month:
     * - 'month_contract_plan'
     * - 'month_contract_fact'
     * - 'month_register_fact'
     *
     * @return void
     */
    public function calculatePlanMonthlyProcessCounts()
    {
        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            $monthName = $month['name'];

            // Calculate totals for the current month based on Country codes
            [$contractPlanCount, $contractFactCount, $registerFactCount] = $this->sumMonthlyCountsForCountryCodes($monthName);

            // Assign totals to the current model instance
            $this->{$monthName . '_contract_plan'} = $contractPlanCount;
            $this->{$monthName . '_contract_fact'} = $contractFactCount;
            $this->{$monthName . '_register_fact'} = $registerFactCount;
        }
    }

    /**
     * Sum the 'contract_plan', 'contract_fact', and 'register_fact' counts for a specific month
     * across all related Country codes.
     *
     * @param  string  $monthName
     * @return array  [contractPlanCount, contractFactCount, registerFactCount]
     */
    private function sumMonthlyCountsForCountryCodes($monthName)
    {
        $contractPlanCount = 0;
        $contractFactCount = 0;
        $registerFactCount = 0;

        // Iterate through related Country codes to accumulate monthly counts
        foreach ($this->countryCodes as $country) {
            $contractPlanCount += $country->{$monthName . '_contract_plan'} ?? 0;
            $contractFactCount += $country->{$monthName . '_contract_fact'} ?? 0;
            $registerFactCount += $country->{$monthName . '_register_fact'} ?? 0;
        }

        return [$contractPlanCount, $contractFactCount, $registerFactCount];
    }
}
