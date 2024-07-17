<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Product::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Product::mergeQueryingParamsToRequest($request);
        $records = Product::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('products_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('products.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Product::mergeQueryingParamsToRequest($request);
        $records = Product::getRecordsFinalized($request, Product::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('products_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('products.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('products.create');
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
        $similarRecords = Product::getSimilarRecords($request);

        return view('products.similar-records', compact('similarRecords'));
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        Product::createFromRequest($request);

        return to_route('products.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Product $instance)
    {
        return view('products.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(ProductUpdateRequest $request, Product $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Product::mergeExportQueryingParamsToRequest($request);
        $records = Product::getRecordsFinalized($request, finaly: 'query');

        return Product::exportRecordsAsExcel($records);
    }
}
