<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\CountryCode;
use App\Models\MarketingAuthorizationHolder;
use App\Models\Plan;
use App\Support\Helper;
use App\Support\Traits\DestroysModelRecords;
use Illuminate\Http\Request;

class PlanController222 extends Controller
{
    use DestroysModelRecords;

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
        $records = Plan::getAll();

        foreach ($records as $record) {
            $record->makeAllCalculations($request);
        }

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

    public function show(Request $request)
    {
        Plan::mergeDefaultParamsToRequest($request);
        $plan = Plan::getByYearFromRequest($request);
        $plan->makeAllCalculationsAndAddLinksFromRequest($request);

        $months = Helper::collectCalendarMonths();

        return view('plan.show', compact('request', 'plan', 'months'));
    }

    /*
    |--------------------------------------------------------------------------
    | Country code routes
    |--------------------------------------------------------------------------
    */

    public function countryCodesIndex(Plan $plan)
    {
        $plan->loadMarketingAuthorizationHoldersOfCountries();
        $records = $plan->countryCodes;

        return view('plan.country-codes.index', compact('plan', 'records'));
    }

    public function countryCodesCreate(Plan $plan)
    {
        return view('plan.country-codes.create', compact('plan'));
    }

    public function countryCodesStore(Request $request, Plan $plan)
    {
        $countryCode = CountryCode::attachToPlanFromRequest($request, $plan);
        MarketingAuthorizationHolder::attachToPlanOnCountryCodeStore($request, $plan, $countryCode);

        return to_route('plan.country.codes.index', ['plan' => $plan->id]);
    }

    public function countryCodesEdit(Plan $plan, CountryCode $countryCode)
    {
        $instance = $plan->countryCodes()->where('country_codes.id', $countryCode->id)->first();

        return view('plan.country-codes.edit', compact('plan', 'instance'));
    }

    public function countryCodesUpdate(Request $request, Plan $plan, CountryCode $countryCode)
    {
        $plan->countryCodes()->updateExistingPivot($countryCode->id, [
            'comment' => $request->comment,
        ]);

        return redirect($request->input('previous_url'));
    }

    public function countryCodesDestroy(Request $request, Plan $plan)
    {
        $countryCodeIDs = $request->ids;

        foreach ($countryCodeIDs as $countryCodeID) {
            $plan->detachCountryCodeByID($countryCodeID);
        }

        return to_route('plan.country.codes.index', ['plan' => $plan->id]);
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

    public function marketingAuthorizationHoldersCreate(Plan $plan, CountryCode $countryCode)
    {
        return view('plan.marketing-authorization-holders.create', compact('plan', 'countryCode'));
    }

    public function marketingAuthorizationHoldersStore(Request $request, Plan $plan, CountryCode $countryCode)
    {
        MarketingAuthorizationHolder::attachToPlanFromRequest($plan, $countryCode, $request);

        return to_route('plan.marketing.authorization.holders.index', ['plan' => $plan->id, 'countryCode' => $countryCode->id]);
    }

    public function marketingAuthorizationHoldersEdit(Plan $plan, CountryCode $countryCode, MarketingAuthorizationHolder $marketingAuthorizationHolder)
    {
        $marketingAuthorizationHolders = $plan->marketingAuthorizationHoldersForCountryCode($countryCode->id);
        $instance = $marketingAuthorizationHolders->where('marketing_authorization_holders.id', $marketingAuthorizationHolder->id)->first();

        return view('plan.marketing-authorization-holders.edit', compact('plan', 'countryCode', 'instance'));
    }

    public function marketingAuthorizationHoldersUpdate(Request $request, Plan $plan, CountryCode $countryCode, MarketingAuthorizationHolder $marketingAuthorizationHolder)
    {
        $marketingAuthorizationHolder->updateForPlanFromRequest($plan, $countryCode, $request);

        return redirect($request->input('previous_url'));
    }

    public function marketingAuthorizationHoldersDestroy(Request $request, Plan $plan, CountryCode $countryCode)
    {
        $marketingAuthorizationHolderIDs = $request->ids;

        $plan->detachMarketingAuthorizationHolders($countryCode, $marketingAuthorizationHolderIDs);

        return to_route('plan.marketing.authorization.holders.index', ['plan' => $plan->id, 'countryCode' => $countryCode->id]);
    }
}
