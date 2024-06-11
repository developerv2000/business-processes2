<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Http\Requests\ProcessStoreRequest;
use App\Http\Requests\ProcessUpdateRequest;
use App\Models\CountryCode;
use App\Models\ProcessStatus;
use App\Models\Product;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Process::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Process::mergeQueryingParamsToRequest($request);
        $records = Process::getRecordsFinalized($request, finaly: 'paginate');
        Process::addGeneralStatusPeriodsForRecords($records);
        // dd($records[0]->general_status_periods);

        $allTableColumns = $request->user()->collectAllTableColumns('processes_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('processes.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Process::mergeQueryingParamsToRequest($request);
        $records = Process::getRecordsFinalized($request, Process::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('processes_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('processes.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->load('manufacturer');

        return view('processes.create', compact('product'));
    }

    /**
     * Return required stage inputs for each stage
     * on status select change ajax request
     */
    public function getCreateFormStageInputs(Request $request)
    {
        $product = Product::find($request->product_id);
        $status = ProcessStatus::find($request->status_id);
        $stage = $status->generalStatus->stage;

        return view('processes.partials.create-form-stage-inputs', compact('product', 'stage'));
    }

    /**
     * Return required forecast inputs for each countries separately,
     * on status select & search countries select changes ajax request
     */
    public function getCreateFormForecastInputs(Request $request)
    {
        $status = ProcessStatus::find($request->status_id);
        $stage = $status->generalStatus->stage;

        $countryCodesIDs = $request->input('country_code_ids', []);
        $selectedCountryCodes = CountryCode::whereIn('id', $countryCodesIDs)->pluck('name');

        return view('processes.partials.create-form-forecast-inputs', compact('stage', 'selectedCountryCodes'));
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(ProcessStoreRequest $request)
    {
        Process::createFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Process $instance)
    {
        $product = $instance->product;

        return view('processes.edit', compact('instance', 'product'));
    }

    /**
     * Return required stage inputs for each stage
     * on status select change ajax request
     */
    public function getEditFormStageInputs(Request $request)
    {
        $instance = Process::find($request->process_id);
        $product = Product::find($request->product_id);
        $status = ProcessStatus::find($request->status_id);
        $stage = $status->generalStatus->stage;

        return view('processes.partials.edit-form-stage-inputs', compact('instance', 'product', 'stage'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(ProcessUpdateRequest $request, Process $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Process::mergeExportQueryingParamsToRequest($request);
        $records = Process::getRecordsFinalized($request, finaly: 'query');

        return Process::exportRecordsAsExcel($records);
    }
}
