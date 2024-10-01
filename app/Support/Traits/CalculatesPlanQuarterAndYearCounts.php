<?php

namespace App\Support\Traits;

use App\Support\Helper;

/**
 * Trait CalculatesPlanQuarterAndYearCounts
 *
 * Provides functionality to calculate quarterly and yearly process counts
 * for 'contract_plan', 'contract_fact', and 'register_fact'.
 *
 * Used by MarketingAuthorizationHolder, Country, and Plan models.
 *
 * @package App\Support\Traits
 */
trait CalculatesPlanQuarterAndYearCounts
{
    /**
     * Calculate quarterly process counts ('contract_plan', 'contract_fact', and 'register_fact')
     * by summing up the values from each month of the quarter.
     *
     * This method dynamically sets the following properties for each quarter:
     *  - 'quarter_X_contract_plan'
     *  - 'quarter_X_contract_fact'
     *  - 'quarter_X_register_fact'
     *
     * Where X is the quarter number (1 to 4).
     */
    public function calculatePlanQuartersProcessCounts()
    {
        $months = Helper::collectCalendarMonths();

        // Iterate through each quarter (1 to 4)
        for ($quarter = 1, $monthIndex = 0; $quarter <= 4; $quarter++) {
            $contractPlanCount = 0;
            $contractFactCount = 0;
            $registerFactCount = 0;

            // Sum values for the 3 months of the quarter
            for ($i = 0; $i < 3; $i++, $monthIndex++) {
                $monthName = $months[$monthIndex]['name'];

                // Ensure the monthly properties exist, default to 0 if missing
                $contractPlan = $this->{$monthName . '_contract_plan'} ?? 0;
                $contractFact = $this->{$monthName . '_contract_fact'} ?? 0;
                $registerFact = $this->{$monthName . '_register_fact'} ?? 0;

                // Accumulate the counts, ensuring numeric values
                $contractPlanCount += is_numeric($contractPlan) ? $contractPlan : 0;
                $contractFactCount += is_numeric($contractFact) ? $contractFact : 0;
                $registerFactCount += is_numeric($registerFact) ? $registerFact : 0;
            }

            // Set the accumulated quarterly values
            $this->{'quarter_' . $quarter . '_contract_plan'} = $contractPlanCount;
            $this->{'quarter_' . $quarter . '_contract_fact'} = $contractFactCount;
            $this->{'quarter_' . $quarter . '_register_fact'} = $registerFactCount;
        }
    }

    /**
     * Calculate yearly process counts by summing up the quarterly values
     * for 'contract_plan', 'contract_fact', and 'register_fact'.
     *
     * This method sets the following properties on the model:
     *  - 'year_contract_plan'
     *  - 'year_contract_fact'
     *  - 'year_register_fact'
     */
    public function calculatePlanYearProcessCounts()
    {
        $contractPlanCount = 0;
        $contractFactCount = 0;
        $registerFactCount = 0;

        // Sum values from all four quarters
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $contractPlanCount += $this->{'quarter_' . $quarter . '_contract_plan'} ?? 0;
            $contractFactCount += $this->{'quarter_' . $quarter . '_contract_fact'} ?? 0;
            $registerFactCount += $this->{'quarter_' . $quarter . '_register_fact'} ?? 0;
        }

        // Set the accumulated yearly values
        $this->year_contract_plan = $contractPlanCount;
        $this->year_contract_fact = $contractFactCount;
        $this->year_register_fact = $registerFactCount;
    }

    /**
     * Calculate yearly percentages for 'contract_fact' and 'register_fact'
     * based on the 'contract_plan' total.
     *
     * Note: 'contract_plan' is used to calculate both 'contract_fact_percentage'
     * and 'register_fact_percentage'.
     *
     * This method sets the following properties on the model:
     *  - 'year_contract_fact_percentage'
     *  - 'year_register_fact_percentage'
     */
    public function calculatePlanYearPercentages()
    {
        // Helper function to calculate percentages safely
        $calculatePercentage = function ($fact, $plan) {
            return ($plan > 0) ? round(($fact * 100) / $plan, 2) : 0;
        };

        // 1. Calculate contract fact percentage based on 'year_contract_plan'
        $contractPlan = $this->year_contract_plan ?? 0; // Ensure the 'year_contract_plan' exists and is numeric
        $contractFact = $this->year_contract_fact ?? 0; // Ensure the 'year_contract_fact' exists and is numeric
        $this->year_contract_fact_percentage = $calculatePercentage($contractFact, $contractPlan);

        // 2. Calculate register fact percentage using the same 'year_contract_plan'
        $registerFact = $this->year_register_fact ?? 0; // Ensure the 'year_register_fact' exists and is numeric
        $this->year_register_fact_percentage = $calculatePercentage($registerFact, $contractPlan);
    }
}
