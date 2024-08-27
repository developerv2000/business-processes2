<?php

namespace App\Models;

use App\Support\Abstracts\UsageCountableModel;
use App\Support\Helper;

class CountryCode extends UsageCountableModel
{
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

    public function marketingAuthorizationHoldersForPlan($planID)
    {
        return $this->belongsToMany(MarketingAuthorizationHolder::class, 'plan_country_code_marketing_authorization_holder')
            ->wherePivot('plan_id', $planID)
            ->withPivot(Plan::getPivotColumnNames());
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

    public function calculateMonthProcessesCountForPlan()
    {
        $months = Helper::collectCalendarMonths();

        foreach ($months as $month) {
            $monthName = $month['name'];
            $contractPlanCount = 0;
            $contractFactCount = 0;
            $registerFactCount = 0;

            foreach ($this->plan_marketing_authorization_holders as $mah) {
                $contractPlanCount += $mah->pivot->{$monthName . '_contract_plan'};
                $contractFactCount += $mah->pivot->{$monthName . '_contract_fact'};
                $registerFactCount += $mah->pivot->{$monthName . '_register_fact'};
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
