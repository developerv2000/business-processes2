<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Models\Manufacturer;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Manufacturer::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Manufacturer::mergeQueryingParamsToRequest($request);
        $records = Manufacturer::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('manufacturers_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('manufacturers.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Manufacturer::mergeQueryingParamsToRequest($request);
        $records = Manufacturer::getRecordsFinalized($request, Manufacturer::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('manufacturers_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('manufacturers.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('manufacturers.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(ManufacturerStoreRequest $request)
    {
        Manufacturer::createFromRequest($request);

        return to_route('manufacturers.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Manufacturer $instance)
    {
        return view('manufacturers.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(ManufacturerUpdateRequest $request, Manufacturer $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Manufacturer::mergeExportQueryingParamsToRequest($request);
        $records = Manufacturer::getRecordsFinalized($request, finaly: 'query');

        return Manufacturer::exportRecordsAsExcel($records);
    }
}
