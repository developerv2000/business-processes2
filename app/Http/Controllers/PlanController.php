<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\CountryCode;
use App\Models\Plan;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Plan::class; // used in multiple destroy/restore traits

    /*
    |--------------------------------------------------------------------------
    | Plan routes
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Plan::mergeQueryingParamsToRequest($request);
        $records = Plan::getAll();

        return view('plan.index', compact('request', 'records'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('plan.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(PlanStoreRequest $request)
    {
        Plan::createFromRequest($request);

        return to_route('plan.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Plan $instance)
    {
        return view('plan.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(PlanUpdateRequest $request, Plan $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /*
    |--------------------------------------------------------------------------
    | Country code routes
    |--------------------------------------------------------------------------
    */

    public function countryCodesIndex(Plan $plan)
    {
        $records = $plan->countryCodes;
        CountryCode::loadMarketingAuthorizationHoldersForPlan($records, $plan);

        return view('plan.country-codes.index', compact('plan', 'records'));
    }

    public function countryCodesCreate(Plan $plan)
    {
        return view('plan.country-codes.create', compact('plan'));
    }

    public function countryCodesStore(Request $request, Plan $plan)
    {
        $countryCode = $plan->storeCountryCodeFromRequest($request);
        $countryCode->attachMarketingAuthorizationHoldersForPlan($plan, $request->marketing_authorization_holder_ids);

        return to_route('plan.country.codes.index', ['plan' => $plan->id]);
    }

    public function countryCodesEdit(Plan $plan, CountryCode $countryCode)
    {
        $instance = $plan->countryCodes()->where('id', $countryCode->id)->first();

        return view('plan.country-codes.edit', compact('plan', 'instance'));
    }

    public function countryCodesUpdate(Request $request, Plan $plan, CountryCode $countryCode)
    {
        $plan->countryCodes()->updateExistingPivot($countryCode->id, [
            'comment' => $request->comment,
        ]);

        return redirect($request->input('previous_url'));
    }

    /*
    |--------------------------------------------------------------------------
    | Marketing authorization holders routes
    |--------------------------------------------------------------------------
    */

    public function marketingAuthorizationHoldersIndex(Plan $plan, CountryCode $countryCode)
    {
        $records = $countryCode->marketingAuthorizationHoldersForPlan($plan->id)->get();

        return view('plan.marketing-authorization-holders.index', compact('plan', 'countryCode', 'records'));
    }

    public function marketingAuthorizationHoldersCreate(Plan $plan)
    {
        return view('plan.marketing-authorization-holders.create', compact('plan'));
    }

    public function marketingAuthorizationHoldersStore(Request $request, Plan $plan)
    {
        $countryCode = $plan->storeCountryCodeFromRequest($request);
        $countryCode->attachMarketingAuthorizationHoldersForPlan($plan, $request->marketing_authorization_holder_ids);

        return to_route('plan.marketing.authorization.holders.index', ['plan' => $plan->id]);
    }

    public function marketingAuthorizationHoldersEdit(Plan $plan, CountryCode $countryCode)
    {
        $instance = $plan->countryCodes()->where('id', $countryCode->id)->first();

        return view('plan.marketing-authorization-holders.edit', compact('plan', 'instance'));
    }

    public function marketingAuthorizationHoldersUpdate(Request $request, Plan $plan, CountryCode $countryCode)
    {
        $plan->countryCodes()->updateExistingPivot($countryCode->id, [
            'comment' => $request->comment,
        ]);

        return redirect($request->input('previous_url'));
    }
}
