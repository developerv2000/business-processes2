<?php

namespace App\Http\Controllers;

use App\Models\Kvpp;
use App\Http\Requests\KvppUpdateRequest;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class KvppController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Kvpp::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Kvpp::mergeQueryingParamsToRequest($request);
        $records = Kvpp::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('kvpp_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('kvpp.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Kvpp::mergeQueryingParamsToRequest($request);
        $records = Kvpp::getRecordsFinalized($request, Kvpp::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('kvpp_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('kvpp.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('kvpp.create');
    }

    /**
     * Get similar records based on the provided request data.
     *
     * This method is used for AJAX requests to retrieve similar records on the create form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSimilarRecords(Request $request)
    {
        $similarRecords = Kvpp::getSimilarRecords($request);

        return view('kvpp.similar-records', compact('similarRecords'));
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(Request $request)
    {
        Kvpp::createFromRequest($request);

        return to_route('kvpp.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Kvpp $instance)
    {
        return view('kvpp.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(KvppUpdateRequest $request, Kvpp $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Kvpp::mergeExportQueryingParamsToRequest($request);
        $records = Kvpp::getRecordsFinalized($request, finaly: 'query');

        return Kvpp::exportRecordsAsExcel($records);
    }
}
