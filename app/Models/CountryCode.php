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

    public function makeAllPlanCalculations($request)
    {

    }
}
