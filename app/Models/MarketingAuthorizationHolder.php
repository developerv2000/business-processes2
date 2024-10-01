<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use App\Support\Helper;
use App\Support\Traits\CalculatesPlanQuarterAndYearCounts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MarketingAuthorizationHolder extends Model implements TemplatedModelInterface
{
    use HasFactory;
    use CalculatesPlanQuarterAndYearCounts;

    public $timestamps = false;
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_country_code_marketing_authorization_holder');
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy('id')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->processes()->count()
            + $this->kvpps()->count();
    }

    /**
     * Used in route plan.country.codes.store
     */
    public static function attachManyToPlanOnCountryCodeStore($request, $plan, $countryCode)
    {
        $marketingAuthorizationHolderIDs = $request->input('marketing_authorization_holder_ids', []);
        $pivotData = ['country_code_id' => $countryCode->id];

        foreach ($marketingAuthorizationHolderIDs as $mahID) {
            $plan->marketingAuthorizationHolders()->attach($mahID, $pivotData);
        }
    }

    /**
     * Used in route plan.marketing.authorization.holders.store
     */
    public static function attachToPlanFromRequest($plan, $countryCode, Request $request)
    {
        // Get the relevant data from the request explicitly
        ['marketing_authorization_holder_id' => $MAHId] = $request->only('marketing_authorization_holder_id');

        // Define the pivot data explicitly, leaving out unnecessary fields
        $pivotData = array_merge(
            $request->except(['marketing_authorization_holder_id', '_token', 'previous_url']),
            ['country_code_id' => $countryCode->id]
        );

        // Attach the related MAH with the pivot data
        $plan->marketingAuthorizationHolders()->attach($MAHId, $pivotData);
    }

    /**
     * Used in route plan.marketing.authorization.holders.update
     */
    public function updateForPlanFromRequest($plan, $countryCode, $request)
    {
        $pivotData = $request->except('readonly', '_token', 'previous_url', '_method');

        $plan->MAHsOfCountryCode($countryCode)->updateExistingPivot($this->id, $pivotData);
    }

    /**
     * Perform all necessary plan calculations for a Marketing Authorization Holder (MAH).
     *
     * This method handles:
     * 1. Preparing the contract plan calculations based on the provided request and plan.
     * 2. Calculating monthly, quarterly, and yearly process counts.
     * 3. Calculating yearly percentages.
     *
     * @param \Illuminate\Http\Request $request The request object containing user inputs
     * @param Plan $plan The plan object associated with the MAH
     */
    public function makeAllPlanCalculations($request, $plan)
    {
        // Step 1: Prepare contract plan calculations based on the provided request and plan
        $this->prepareForPlanCalculations($request, $plan);

        // Step 2: Calculate monthly process counts
        $this->calculatePlanMonthlyProcessCounts($request, $plan);

        // Step 3: Add monthly process count links
        $this->addPlanMonthlyProcessCountLinks($request, $plan);

        // Step 4: Calculate quarterly process counts from monthly data
        $this->calculatePlanQuartersProcessCounts();

        // Step 5: Calculate yearly process counts from monthly and quarterly data
        $this->calculatePlanYearProcessCounts();

        // Step 6: Calculate percentages for yearly process counts (e.g., success rates)
        $this->calculatePlanYearPercentages();
    }

    /**
     * Prepare contract plan calculations for a Marketing Authorization Holder (MAH)
     * based on the specified manufacturer country (if any) and the plan's pivot data.
     *
     * @param Request $request The request object containing inputs, including 'specific_manufacturer_country'
     * @param Plan $plan The plan object associated with the MAH (used via pivot table data)
     */
    public function prepareForPlanCalculations($request, $plan)
    {
        // Get the specific manufacturer country from the request, if provided
        $specificManufacturerCountry = $request->input('specific_manufacturer_country');

        // Retrieve the collection of calendar months
        $months = Helper::collectCalendarMonths();

        // Determine which contract plans to calculate based on the manufacturer country
        switch ($specificManufacturerCountry) {
            case null:
                // If no specific manufacturer country is provided:
                // Sum both Europe and India contract plans for each month
                foreach ($months as $month) {
                    $monthName = $month['name'];
                    $this->{$monthName . '_contract_plan'} = $this->pivot->{$monthName . '_europe_contract_plan'}
                        + $this->pivot->{$monthName . '_india_contract_plan'};
                }
                break;

            case 'EUROPE':
                // If manufacturer country is Europe:
                // Set only the Europe contract plans for each month
                foreach ($months as $month) {
                    $monthName = $month['name'];
                    $this->{$monthName . '_contract_plan'} = $this->pivot->{$monthName . '_europe_contract_plan'};
                }
                break;

            case 'INDIA':
                // If manufacturer country is India:
                // Set only the India contract plans for each month
                foreach ($months as $month) {
                    $monthName = $month['name'];
                    $this->{$monthName . '_contract_plan'} = $this->pivot->{$monthName . '_india_contract_plan'};
                }
                break;
        }
    }

    /**
     * Calculate MAH`s contracted and registered processes count for each month of the year.
     *
     * Adds below properties to the current MAH, For each month of the year:
     * $month['name'] . '_contract_fact'
     * $month['name'] . '_register_fact'
     */
    public function calculatePlanMonthlyProcessCounts($request, $plan)
    {
        // Prepare the base query for filtering records
        $baseQuery = Process::where([
            'marketing_authorization_holder_id' => $this->id,
            'country_code_id' => $this->pivot->country_code_id,
        ]);

        // Filter specific manufacturer country (India, Europe or NULL)
        $baseQuery = Process::filterSpecificManufacturerCountry($request, $baseQuery);

        // Prepare contracted and registered process queries based on plan status
        $contractedQuery = Process::filterRecordsByPlanStatus(clone $baseQuery, true, null);
        $registeredQuery = Process::filterRecordsByPlanStatus(clone $baseQuery, null, true);

        // Loop through each month and calculate the processes counts
        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            // 1. Contract Fact
            // Clone and apply month/year filters for contracted processes
            $contractedQueryClone = clone $contractedQuery;
            $contractedQueryClone = Process::filterRecordsContractedOnRequestedMonthAndYear($contractedQueryClone, $plan->year, $month['number']);
            $contractedProcessesCount = $contractedQueryClone->count();
            $this->{$month['name'] . '_contract_fact'} = $contractedProcessesCount;

            // 2. Register Fact
            // Clone and apply month/year filters for registered processes
            $registeredQueryClone = clone $registeredQuery;
            $registeredQueryClone = Process::filterRecordsRegisteredOnRequestedMonthAndYear($registeredQueryClone, $plan->year, $month['number']);
            $registeredProcessesCount = $registeredQueryClone->count();
            $this->{$month['name'] . '_register_fact'} = $registeredProcessesCount;
        }
    }

    /**
     * Generate process count links (processes.index) for each month and store them in the model.
     *
     * This method sets the following properties for each month:
     * - 'month_contract_fact_link'
     * - 'month_register_fact_link'
     *
     * @param \Illuminate\Http\Request $request The request object
     * @param Plan $plan The plan object
     * @return void
     */
    public function addPlanMonthlyProcessCountLinks($request, $plan)
    {
        // Get all months and the requested year
        $months = Helper::collectCalendarMonths();

        // Build the base query parameters for generating process links.
        $baseQueryParams = [
            'marketing_authorization_holder_id[]' => $this->id,
            'country_code_id[]' => $this->pivot->country_code_id,
            'specific_manufacturer_country' => $request->input('specific_manufacturer_country'),
        ];

        // Loop through each month and generate links
        foreach ($months as $month) {
            $monthNumber = $month['number'];

            // Generate contracted processes link and assign it to the model
            $contractedParams = array_merge($baseQueryParams, [
                'contracted_in_plan' => true,
                'contracted_on_requested_month_and_year' => true,
                'contracted_month' => $monthNumber,
                'contracted_year' => $plan->year,
            ]);
            $contractedProcessesLink = route('processes.index', $contractedParams);
            $this->{$month['name'] . '_contract_fact_link'} = $contractedProcessesLink;

            // Generate registered processes link and assign it to the pivot
            $registeredParams = array_merge($baseQueryParams, [
                'registered_in_plan' => true,
                'registered_on_requested_month_and_year' => true,
                'registered_month' => $monthNumber,
                'registered_year' => $plan->year,
            ]);
            $registeredProcessesLink = route('processes.index', $registeredParams);
            $this->pivot->{$month['name'] . '_register_fact_link'} = $registeredProcessesLink;
        }
    }
}
