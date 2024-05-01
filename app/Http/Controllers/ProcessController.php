<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Http\Requests\ProcessStoreRequest;
use App\Http\Requests\ProcessUpdateRequest;
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
    public function create()
    {
        return view('processes.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(ProcessStoreRequest $request)
    {
        Process::createFromRequest($request);

        return to_route('processes.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Process $instance)
    {
        return view('processes.edit', compact('instance'));
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
