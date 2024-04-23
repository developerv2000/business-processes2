<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Models\Manufacturer;
use App\Models\User;
use App\Support\Traits\MultipleDestroyable;
use App\Support\Traits\MultipleRestoreable;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    use MultipleDestroyable;
    use MultipleRestoreable;

    public $model = Manufacturer::class; // used in destroy/restore traits

    public function index(Request $request)
    {
        // Merge additional query parameters to the requests
        Manufacturer::mergeQueryParamsToRequest($request);

        $items = Manufacturer::getItemsFinalized($request);
        $allTableColumns = $request->user()->collectAllTableColumns('manufacturers_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('manufacturers.index', compact('request', 'items', 'allTableColumns', 'visibleTableColumns'));
    }

    public function trash(Request $request)
    {
        // Merge additional query parameters to the requests
        Manufacturer::mergeQueryParamsToRequest($request);

        $items = Manufacturer::getItemsFinalized($request, Manufacturer::onlyTrashed());
        $allTableColumns = $request->user()->collectAllTableColumns('manufacturers_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('manufacturers.trash', compact('request', 'items', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManufacturerStoreRequest $request)
    {
        Manufacturer::createFromRequest($request);

        return to_route('manufacturers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacturer $instance)
    {
        return view('manufacturers.edit', compact('instance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManufacturerUpdateRequest $request, Manufacturer $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    public function export(Request $request)
    {
        // Merge export parameters into the request from requests previous url query.
        Manufacturer::mergeExportParamsToRequest($request);

        $items = Manufacturer::getItemsFinalized($request, finaly: 'query');

        return Manufacturer::exportItemsAsExcel($items);
    }
}
