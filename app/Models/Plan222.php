<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Plan222 extends CommentableModel
{
    use HasFactory;

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

    /**
     * Return marketing authorization holders for specific country code
     */
    public function MAHsOfCountryCode($countryCode)
    {
        return $this->belongsToMany(MarketingAuthorizationHolder::class, 'plan_country_code_marketing_authorization_holder')
            ->wherePivot('country_code_id', $countryCode->id)
            ->withPivot(self::getPivotColumnNames());
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
                $instance->detachCountryCodeByID($countryCode->id);
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

    /**
     * $request->year is used in many relation calculations
     */
    public static function mergeDefaultParamsToRequest($request)
    {
        $request->mergeIfMissing([
            'year' => date('Y'),
        ]);
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

    public function makeAllYearCalculations($request)
    {
        $this->loadMAHsOfCountryCodes();

        foreach ($this->countryCodes as $countryCode) {
            foreach ($countryCode->marketing_authorization_holders as $mah) {
                $mah->makeAllPlanCalculations($request);

                $mah->calculatePlanMonthProcessesCountFromRequest($request);
                $mah->addPlanProcesseslinkFromRequest($request);
                $mah->calculatePlanAllPercentages($request);

                $mah->calculatePlanQuoterProcessesCounts();
                $mah->calculatePlanQuoterPercentages();

                $mah->calculatePlanYearProcessCounts();
                $mah->calculatePlanYearPercentages();
            }

            // Calculate country code processes count
            $countryCode->calculateMonthProcessesCountForPlan();
            $countryCode->calculateMonthPercentagesForPlan();

            $countryCode->calculatePlanQuoterProcessesCounts();
            $countryCode->calculatePlanQuoterPercentages();

            $countryCode->calculatePlanYearProcessCounts();
            $countryCode->calculatePlanYearPercentages();
        }

        // Calculate plan processes count
        $this->calculateMonthProcessesCountForPlan();
        $this->calculateMonthPercentagesForPlan();

        $this->calculatePlanQuoterProcessesCounts();
        $this->calculatePlanQuoterPercentages();

        $this->calculatePlanYearProcessCounts();
        $this->calculatePlanYearPercentages();
    }

    /**
     * Used in route plan.country.codes.destroy
     */
    public function detachCountryCodeByID($countryCodeID)
    {
        $countryCode = CountryCode::find($countryCodeID);
        $marketingAuthorizationHolders = $countryCode->marketingAuthorizationHoldersForPlan($this->id)->get();
        $marketingAuthorizationHolderIDs = $marketingAuthorizationHolders->pluck('id');

        // Detach marketing authorization holders first
        $this->detachMarketingAuthorizationHolders($countryCode, $marketingAuthorizationHolderIDs);

        // Then detach country code
        $this->countryCodes()->detach([$countryCodeID]);
    }

    /**
     * Used in route plan.marketing.authorization.holders.destroy
     * and plan.country.codes.destroy
     */
    public function detachMarketingAuthorizationHolders($countryCode, $marketingAuthorizationHolderIDs)
    {
        foreach ($marketingAuthorizationHolderIDs as $mahID) {
            DB::table('plan_country_code_marketing_authorization_holder')->where([
                'plan_id' => $this->id,
                'country_code_id' => $countryCode->id,
                'marketing_authorization_holder_id' => $mahID,
            ])->delete();
        }
    }

    /**
     * Return array of the pivot column names for 'MAHsOfCountryCode' relationship
     * 'plan_country_code_marketing_authorization_holder' table
     */
    public static function getPivotColumnNamesForMAH(): array
    {
        return [
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

    public function calculateMonthProcessesCountForPlan()
    {
        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            $monthName = $month['name'];
            $contractPlanCount = 0;
            $contractFactCount = 0;
            $registerFactCount = 0;

            foreach ($this->countryCodes as $country) {
                $contractPlanCount += $country->{$monthName . '_contract_plan'};
                $contractFactCount += $country->{$monthName . '_contract_fact'};
                $registerFactCount += $country->{$monthName . '_register_fact'};
            }

            $this->{$monthName . '_contract_plan'} = $contractPlanCount;
            $this->{$monthName . '_contract_fact'} = $contractFactCount;
            $this->{$monthName . '_register_fact'} = $registerFactCount;
        }
    }

    public function calculateMonthPercentagesForPlan()
    {
        // Get all calendar months
        $months = Helper::collectCalendarMonths();

        // Loop through each month and calculate percentages
        foreach ($months as $month) {
            $monthName = $month['name'];

            // 1. Calculate contracted processes percentage
            $monthContractPlan = $this->{$monthName . '_contract_plan'};
            $monthContractFact = $this->{$monthName . '_contract_fact'};

            // Avoid division by zero error
            if ($monthContractPlan > 0) {
                $monthContractPercentage = round(($monthContractFact * 100) / $monthContractPlan, 2);
            } else {
                $monthContractPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->{$monthName . '_contract_fact_percentage'} = $monthContractPercentage;

            // 2. Calculate registered processes percentage
            $monthContractPlan = $this->{$monthName . '_contract_plan'};
            $monthRegisterFact = $this->{$monthName . '_register_fact'};

            // Avoid division by zero error
            if ($monthContractPlan > 0) {
                $monthRegisterPercentage = round(($monthRegisterFact * 100) / $monthContractPlan, 2);
            } else {
                $monthRegisterPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->{$monthName . '_register_fact_percentage'} = $monthRegisterPercentage;
        }
    }

    public function calculatePlanQuoterProcessesCounts()
    {
        // Get all calendar months
        $months = Helper::collectCalendarMonths();

        // Iterate through the 4 quoters (quarters of the year)
        for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++) {
            $contractPlanCount = 0;
            $contractFactCount = 0;
            $registerFactCount = 0;

            // Loop through 3 months for each quoter (quarter)
            for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++, $monthIndex++) {
                $monthName = $months[$monthIndex]['name'];

                // Ensure the properties exist before accessing
                $contractPlan = $this->{$monthName . '_contract_plan'} ?? 0;
                $contractFact = $this->{$monthName . '_contract_fact'} ?? 0;
                $registerFact = $this->{$monthName . '_register_fact'} ?? 0;

                // Accumulate the counts
                $contractPlanCount += is_numeric($contractPlan) ? $contractPlan : 0;
                $contractFactCount += is_numeric($contractFact) ? $contractFact : 0;
                $registerFactCount += is_numeric($registerFact) ? $registerFact : 0;
            }

            // Store the accumulated counts in the pivot table
            $this->{'quoter_' . $quoter . '_contract_plan'} = $contractPlanCount;
            $this->{'quoter_' . $quoter . '_contract_fact'} = $contractFactCount;
            $this->{'quoter_' . $quoter . '_register_fact'} = $registerFactCount;
        }
    }

    public function calculatePlanQuoterPercentages()
    {
        for ($quoter = 1; $quoter <= 4; $quoter++) {
            // 1. Calculate contracted processes percentage
            $quoterContractPlan = $this->{'quoter_' . $quoter . '_contract_plan'};
            $quoterContractFact = $this->{'quoter_' . $quoter . '_contract_fact'};

            // Avoid division by zero error
            if ($quoterContractPlan > 0) {
                $quoterContractPercentage = round(($quoterContractFact * 100) / $quoterContractPlan, 2);
            } else {
                $quoterContractPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->{'quoter_' . $quoter . '_contract_fact_percentage'} = $quoterContractPercentage;

            // 2. Calculate registered processes percentage
            $quoterContractPlan = $this->{'quoter_' . $quoter . '_contract_plan'};
            $quoterRegisterFact = $this->{'quoter_' . $quoter . '_register_fact'};

            // Avoid division by zero error
            if ($quoterContractPlan > 0) {
                $quoterRegisterPercentage = round(($quoterRegisterFact * 100) / $quoterContractPlan, 2);
            } else {
                $quoterRegisterPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->{'quoter_' . $quoter . '_register_fact_percentage'} = $quoterRegisterPercentage;
        }
    }

    public function calculatePlanYearProcessCounts()
    {
        $yearContractPlan = 0;
        $yearContractFact = 0;
        $yearRegisterFact = 0;

        for ($quoter = 1; $quoter <= 4; $quoter++) {
            $yearContractPlan += $this->{'quoter_' . $quoter . '_contract_plan'};
            $yearContractFact += $this->{'quoter_' . $quoter . '_contract_fact'};
            $yearRegisterFact += $this->{'quoter_' . $quoter . '_register_fact'};
        }

        $this->year_contract_plan = $yearContractPlan;
        $this->year_contract_fact = $yearContractFact;
        $this->year_register_fact = $yearRegisterFact;
    }

    public function calculatePlanYearPercentages()
    {
        // 1. Calculate contracted processes percentage
        $yearContractPlan = $this->year_contract_plan;
        $yearContractFact = $this->year_contract_fact;

        // Avoid division by zero error
        if ($yearContractPlan > 0) {
            $yearContractPercentage = round(($yearContractFact * 100) / $yearContractPlan, 2);
        } else {
            $yearContractPercentage = 0;
        }

        // Store the calculated percentage in the pivot
        $this->year_contract_fact_percentage = $yearContractPercentage;

        // 2. Calculate registered processes percentage
        $yearContractPlan = $this->year_contract_plan;
        $yearRegisterFact = $this->year_register_fact;

        // Avoid division by zero error
        if ($yearContractPlan > 0) {
            $yearRegisterPercentage = round(($yearRegisterFact * 100) / $yearContractPlan, 2);
        } else {
            $yearRegisterPercentage = 0;
        }

        // Store the calculated percentage in the pivot
        $this->year_register_fact_percentage = $yearRegisterPercentage;
    }
}
