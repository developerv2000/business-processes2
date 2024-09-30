<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketingAuthorizationHolder222 extends Model implements TemplatedModelInterface
{
    use HasFactory;

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

    public function planCountryCodeMarketingAuthorizationHolders()
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
    public static function attachToPlanOnCountryCodeStore($request, $plan, $countryCode)
    {
        $planID = $plan->id;
        $countryCodeID = $countryCode->id;
        $marketingAuthorizationHolderIDs = $request->input('marketing_authorization_holder_ids', []);

        foreach ($marketingAuthorizationHolderIDs as $mahID) {
            DB::table('plan_country_code_marketing_authorization_holder')->insert([
                'plan_id' => $planID,
                'country_code_id' => $countryCodeID,
                'marketing_authorization_holder_id' => $mahID,
            ]);
        }
    }

    /**
     * Used in route plan.marketing.authorization.holders.store
     */
    public static function attachToPlanFromRequest($plan, $countryCode, $request)
    {
        $instance = [
            'plan_id' => $plan->id,
            'country_code_id' => $countryCode->id,
            'marketing_authorization_holder_id' => $request->marketing_authorization_holder_id,
        ];

        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            // Escape column default(0) errors
            if ($request->input($month['name'] . '_contract_plan')) {
                $instance[$month['name'] . '_contract_plan'] = $request->input($month['name'] . '_contract_plan');
            }

            $instance[$month['name'] . '_comment'] = $request->input($month['name'] . '_comment');
        }

        DB::table('plan_country_code_marketing_authorization_holder')->insert($instance);
    }

    /**
     * Used in route plan.marketing.authorization.holders.update
     */
    public function updateForPlanFromRequest($plan, $countryCode, $request)
    {
        $fields = [];

        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            $fields[$month['name'] . '_contract_plan'] = $request->input($month['name'] . '_contract_plan');
            $fields[$month['name'] . '_comment'] = $request->input($month['name'] . '_comment');
        }

        $plan->marketingAuthorizationHoldersForCountryCode($countryCode->id)->updateExistingPivot($this->id, $fields);
    }

    /**
     * Calculate and update the count of contracted and registered processes for each month
     * based on the requested year
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function calculatePlanMonthProcessesCountFromRequest($request)
    {
        // Get all months and the requested year
        $months = Helper::collectCalendarMonths();
        $year = $request->input('year');

        // Prepare the base query for filtering records
        $baseQuery = Process::where([
            'marketing_authorization_holder_id' => $this->id,
            'country_code_id' => $this->pivot->country_code_id,
        ]);

        $baseQuery = Process::filterSpecificManufacturerCountry($request, $baseQuery); // India & Europe

        // Prepare contracted and registered queries based on plan status
        $contractedQuery = Process::filterRecordsByPlanStatus(clone $baseQuery, true, null);
        $registeredQuery = Process::filterRecordsByPlanStatus(clone $baseQuery, null, true);

        // Loop through each month and calculate the counts
        foreach ($months as $month) {
            $monthNumber = $month['number'];
            $monthName = $month['name'];

            // Clone and apply month/year filters for contracted processes
            $contractedQueryClone = clone $contractedQuery;
            $contractedQueryClone = Process::filterRecordsContractedOnRequestedMonthAndYear($contractedQueryClone, $year, $monthNumber);
            $contractedProcessesCount = $contractedQueryClone->count();
            $this->pivot->{$monthName . '_contract_fact'} = $contractedProcessesCount;

            // Clone and apply month/year filters for registered processes
            $registeredQueryClone = clone $registeredQuery;
            $registeredQueryClone = Process::filterRecordsRegisteredOnRequestedMonthAndYear($registeredQueryClone, $year, $monthNumber);
            $registeredProcessesCount = $registeredQueryClone->count();
            $this->pivot->{$monthName . '_register_fact'} = $registeredProcessesCount;
        }
    }

    /**
     * Generate and add plan processes links (contracted and registered) for each month
     * based on the request
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function addPlanProcesseslinkFromRequest($request)
    {
        // Get all months and the requested year
        $months = Helper::collectCalendarMonths();
        $year = $request->input('year');

        // Default query params from the request
        $baseQueryParams = [
            'marketing_authorization_holder_id[]' => $this->id,
            'country_code_id[]' => $this->pivot->country_code_id,
            'specific_manufacturer_country' => $request->input('specific_manufacturer_country'),
        ];

        // Loop through each month and generate links
        foreach ($months as $month) {
            $monthNumber = $month['number'];
            $monthName = $month['name'];

            // Generate contracted processes link
            $contractedParams = array_merge($baseQueryParams, [
                'contracted_in_plan' => true,
                'contracted_on_requested_month_and_year' => true,
                'contracted_month' => $monthNumber,
                'contracted_year' => $year,
            ]);
            $contractedProcessesLink = route('processes.index', $contractedParams);
            $this->pivot->{$monthName . '_contract_fact_link'} = $contractedProcessesLink;

            // Generate registered processes link
            $registeredParams = array_merge($baseQueryParams, [
                'registered_in_plan' => true,
                'registered_on_requested_month_and_year' => true,
                'registered_month' => $monthNumber,
                'registered_year' => $year,
            ]);
            $registeredProcessesLink = route('processes.index', $registeredParams);
            $this->pivot->{$monthName . '_register_fact_link'} = $registeredProcessesLink;
        }
    }

    /**
     * Calculate all the plan percentages (contracted and registered) based on the request
     *
     * @return void
     */
    public function calculatePlanAllPercentages()
    {
        // Get all calendar months
        $months = Helper::collectCalendarMonths();

        // Loop through each month and calculate percentages
        foreach ($months as $month) {
            $monthName = $month['name'];

            // 1. Calculate contracted processes percentage
            $monthContractPlan = $this->pivot->{$monthName . '_contract_plan'};
            $monthContractFact = $this->pivot->{$monthName . '_contract_fact'};

            // Avoid division by zero error
            if ($monthContractPlan > 0) {
                $monthContractPercentage = round(($monthContractFact * 100) / $monthContractPlan, 2);
            } else {
                $monthContractPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->pivot->{$monthName . '_contract_fact_percentage'} = $monthContractPercentage;

            // 2. Calculate registered processes percentage
            $monthContractPlan = $this->pivot->{$monthName . '_contract_plan'};
            $monthRegisterFact = $this->pivot->{$monthName . '_register_fact'};

            // Avoid division by zero error
            if ($monthContractPlan > 0) {
                $monthRegisterPercentage = round(($monthRegisterFact * 100) / $monthContractPlan, 2);
            } else {
                $monthRegisterPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->pivot->{$monthName . '_register_fact_percentage'} = $monthRegisterPercentage;
        }
    }

    /**
     * Calculate the total plan counts (contracted and registered) for each quoter
     *
     * @return void
     */
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
                $contractPlan = $this->pivot->{$monthName . '_contract_plan'} ?? 0;
                $contractFact = $this->pivot->{$monthName . '_contract_fact'} ?? 0;
                $registerFact = $this->pivot->{$monthName . '_register_fact'} ?? 0;

                // Accumulate the counts
                $contractPlanCount += is_numeric($contractPlan) ? $contractPlan : 0;
                $contractFactCount += is_numeric($contractFact) ? $contractFact : 0;
                $registerFactCount += is_numeric($registerFact) ? $registerFact : 0;
            }

            // Store the accumulated counts in the pivot table
            $this->pivot->{'quoter_' . $quoter . '_contract_plan'} = $contractPlanCount;
            $this->pivot->{'quoter_' . $quoter . '_contract_fact'} = $contractFactCount;
            $this->pivot->{'quoter_' . $quoter . '_register_fact'} = $registerFactCount;
        }
    }

    public function calculatePlanQuoterPercentages()
    {
        for ($quoter = 1; $quoter <= 4; $quoter++) {
            // 1. Calculate contracted processes percentage
            $quoterContractPlan = $this->pivot->{'quoter_' . $quoter . '_contract_plan'};
            $quoterContractFact = $this->pivot->{'quoter_' . $quoter . '_contract_fact'};

            // Avoid division by zero error
            if ($quoterContractPlan > 0) {
                $quoterContractPercentage = round(($quoterContractFact * 100) / $quoterContractPlan, 2);
            } else {
                $quoterContractPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->pivot->{'quoter_' . $quoter . '_contract_fact_percentage'} = $quoterContractPercentage;

            // 2. Calculate registered processes percentage
            $quoterContractPlan = $this->pivot->{'quoter_' . $quoter . '_contract_plan'};
            $quoterRegisterFact = $this->pivot->{'quoter_' . $quoter . '_register_fact'};

            // Avoid division by zero error
            if ($quoterContractPlan > 0) {
                $quoterRegisterPercentage = round(($quoterRegisterFact * 100) / $quoterContractPlan, 2);
            } else {
                $quoterRegisterPercentage = 0;
            }

            // Store the calculated percentage in the pivot
            $this->pivot->{'quoter_' . $quoter . '_register_fact_percentage'} = $quoterRegisterPercentage;
        }
    }

    public function calculatePlanYearProcessCounts()
    {
        $yearContractPlan = 0;
        $yearContractFact = 0;
        $yearRegisterFact = 0;

        for ($quoter = 1; $quoter <= 4; $quoter++) {
            $yearContractPlan += $this->pivot->{'quoter_' . $quoter . '_contract_plan'};
            $yearContractFact += $this->pivot->{'quoter_' . $quoter . '_contract_fact'};
            $yearRegisterFact += $this->pivot->{'quoter_' . $quoter . '_register_fact'};
        }

        $this->pivot->year_contract_plan = $yearContractPlan;
        $this->pivot->year_contract_fact = $yearContractFact;
        $this->pivot->year_register_fact = $yearRegisterFact;
    }

    public function calculatePlanYearPercentages()
    {
        // 1. Calculate contracted processes percentage
        $yearContractPlan = $this->pivot->year_contract_plan;
        $yearContractFact = $this->pivot->year_contract_fact;

        // Avoid division by zero error
        if ($yearContractPlan > 0) {
            $yearContractPercentage = round(($yearContractFact * 100) / $yearContractPlan, 2);
        } else {
            $yearContractPercentage = 0;
        }

        // Store the calculated percentage in the pivot
        $this->pivot->year_contract_fact_percentage = $yearContractPercentage;

        // 2. Calculate registered processes percentage
        $yearContractPlan = $this->pivot->year_contract_plan;
        $yearRegisterFact = $this->pivot->year_register_fact;

        // Avoid division by zero error
        if ($yearContractPlan > 0) {
            $yearRegisterPercentage = round(($yearRegisterFact * 100) / $yearContractPlan, 2);
        } else {
            $yearRegisterPercentage = 0;
        }

        // Store the calculated percentage in the pivot
        $this->pivot->year_register_fact_percentage = $yearRegisterPercentage;
    }
}
