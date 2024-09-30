<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

    public function makeAllPlanCalculations($request)
    {
        
    }
}
