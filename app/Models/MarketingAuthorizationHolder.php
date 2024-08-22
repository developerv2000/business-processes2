<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketingAuthorizationHolder extends Model implements TemplatedModelInterface
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

            // Escape column default(0) errors
            if ($request->input($month['name'] . '_register_plan')) {
                $instance[$month['name'] . '_register_plan'] = $request->input($month['name'] . '_register_plan');
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
            $fields[$month['name'] . '_register_plan'] = $request->input($month['name'] . '_register_plan');
            $fields[$month['name'] . '_comment'] = $request->input($month['name'] . '_comment');
        }

        $plan->marketingAuthorizationHoldersForCountryCode($countryCode->id)->updateExistingPivot($this->id, $fields);
    }
}
