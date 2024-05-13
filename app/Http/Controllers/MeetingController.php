<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Http\Requests\MeetingStoreRequest;
use App\Http\Requests\MeetingUpdateRequest;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Meeting::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Meeting::mergeQueryingParamsToRequest($request);
        $records = Meeting::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('meetings_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('meetings.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Meeting::mergeQueryingParamsToRequest($request);
        $records = Meeting::getRecordsFinalized($request, Meeting::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('meetings_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('meetings.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('meetings.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(MeetingStoreRequest $request)
    {
        Meeting::createFromRequest($request);

        return to_route('meetings.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Meeting $instance)
    {
        return view('meetings.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(MeetingUpdateRequest $request, Meeting $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Meeting::mergeExportQueryingParamsToRequest($request);
        $records = Meeting::getRecordsFinalized($request, finaly: 'query');

        return Meeting::exportRecordsAsExcel($records);
    }
}
