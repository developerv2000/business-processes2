<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;
use App\Models\User;
use App\Support\Helper;
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
        // Add additional parameters to the requests query
        Manufacturer::addQueryParamsToRequest($request);

        $items = Manufacturer::getItemsFinalized($request);
        $allTableColumns = $request->user()->collectAllTableColumns('manufacturers_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('manufacturers.index', compact('request', 'items', 'allTableColumns', 'visibleTableColumns'));
    }

    public function trash(Request $request)
    {
        // Add additional parameters to the requests query
        Manufacturer::addQueryParamsToRequest($request);

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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManufacturerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacturer $manufacturer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        //
    }
}
