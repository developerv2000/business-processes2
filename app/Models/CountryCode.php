<?php

namespace App\Models;

use App\Support\Abstracts\UsageCountableModel;
use App\Support\Helper;
use App\Support\Traits\CalculatesPlanQuarterAndYearCounts;

class CountryCode extends UsageCountableModel
{
    use CalculatesPlanQuarterAndYearCounts;

    protected $guarded = ['id'];
    public $timestamps = false;

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
        return $this->belongsToMany(Plan::class);
    }

    public function MAHsOfPlan($plan)
    {
        return $this->belongsToMany(MarketingAuthorizationHolder::class, 'plan_country_code_marketing_authorization_holder')
            ->wherePivot('plan_id', $plan->id)
            ->withPivot(Plan::getPivotColumnNamesForMAH());
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy('usage_count', 'desc')->orderBy('name')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    // Implement method declared in UsageCountableModel abstract class
    public function recalculateUsageCount(): void
    {
        $this->update([
            'usage_count' => $this->processes()->count() + $this->kvpps()->count(),
        ]);
    }

    /**
     * Used in route plan.country.codes.store
     */
    public static function attachToPlanFromRequest($request, $plan)
    {
        $countryCode = self::find($request->country_code_id);

        $plan->countryCodes()->attach($countryCode);

        return $countryCode;
    }

    /**
     * Perform all plan calculations: Monthly, Quarterly, Yearly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function makeAllPlanCalculations($request, $plan)
    {
        // Perform monthly, quarterly, and yearly calculations
        $this->calculatePlanMonthlyProcessCounts();
        $this->calculatePlanQuartersProcessCounts();
        $this->calculatePlanYearProcessCounts();
        $this->calculatePlanYearPercentages();
    }

    /**
     * Calculate the total 'contract_plan', 'contract_fact', and 'register_fact'
     * counts for each month based on related Marketing Authorization Holders (MAH).
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

            // Calculate the totals for the current month
            [$contractPlanCount, $contractFactCount, $registerFactCount] = $this->sumMonthlyCountsForMAHs($monthName);

            // Assign totals to the current model instance
            $this->{$monthName . '_contract_plan'} = $contractPlanCount;
            $this->{$monthName . '_contract_fact'} = $contractFactCount;
            $this->{$monthName . '_register_fact'} = $registerFactCount;
        }
    }

    /**
     * Sum the 'contract_plan', 'contract_fact', and 'register_fact' counts for a specific month
     * across all related Marketing Authorization Holders (MAHs).
     *
     * @param  string  $monthName
     * @return array  [contractPlanCount, contractFactCount, registerFactCount]
     */
    private function sumMonthlyCountsForMAHs($monthName)
    {
        $contractPlanCount = 0;
        $contractFactCount = 0;
        $registerFactCount = 0;

        // Iterate through all marketing authorization holders
        foreach ($this->marketing_authorization_holders as $mah) {
            $contractPlanCount += $mah->{$monthName . '_contract_plan'} ?? 0;
            $contractFactCount += $mah->{$monthName . '_contract_fact'} ?? 0;
            $registerFactCount += $mah->{$monthName . '_register_fact'} ?? 0;
        }

        return [$contractPlanCount, $contractFactCount, $registerFactCount];
    }
}
