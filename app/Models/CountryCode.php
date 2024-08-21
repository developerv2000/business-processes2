<?php

namespace App\Models;

use App\Support\Abstracts\UsageCountableModel;
use Illuminate\Support\Facades\DB;

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

    public static function loadMarketingAuthorizationHoldersForPlan($records, $plan)
    {
        $planID = $plan->id;

        foreach ($records as $instance) {
            $instance->plan_marketing_authorization_holders = $instance->marketingAuthorizationHoldersForPlan($planID)->get();
        }
    }

    public function attachMarketingAuthorizationHoldersForPlan($plan, $marketingAuthorizationHolderIDs = [])
    {
        $planID = $plan->id;

        foreach ($marketingAuthorizationHolderIDs as $mahID) {
            DB::table('plan_country_code_marketing_authorization_holder')->insert([
                'plan_id' => $planID,
                'country_code_id' => $this->id,
                'marketing_authorization_holder_id' => $mahID,
            ]);
        }
    }
}
