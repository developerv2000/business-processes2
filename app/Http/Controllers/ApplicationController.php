<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationUpdateRequest;
use App\Models\Application;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Application::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Application::mergeQueryingParamsToRequest($request);
        $records = Application::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('applications_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('applications.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Application::mergeQueryingParamsToRequest($request);
        $records = Application::getRecordsFinalized($request, Application::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('applications_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('applications.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Application $instance)
    {
        return view('applications.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(ApplicationUpdateRequest $request, Application $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Application::mergeExportQueryingParamsToRequest($request);
        $records = Application::getRecordsFinalized($request, finaly: 'query');

        return Application::exportRecordsAsExcel($records);
    }
}
