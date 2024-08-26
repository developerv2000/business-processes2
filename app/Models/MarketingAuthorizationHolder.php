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

    public function calculatePlanAllProcessesCountFromRequest($request)
    {
        $this->calculatePlanMonthProcessesCountFromRequest($request);
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
        $baseQuery = Process::query();
        $baseQuery = Process::filterSpecificManufacturerCountry($request, $baseQuery);

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
}
