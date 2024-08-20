<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\CountryCode;
use App\Models\Plan;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

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
        CountryCode::loadMarketingAuthorizationHoldersForPlan($records, $plan->id);

        return view('plan.country-codes.index', compact('plan', 'records'));
    }

    public function countryCodesCreate(Plan $plan)
    {
        return view('plan.country-codes.create', compact('plan'));
    }
}
