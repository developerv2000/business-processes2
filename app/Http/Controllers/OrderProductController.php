<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductStoreRequest;
use App\Http\Requests\OrderProductUpdateRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = OrderProduct::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        OrderProduct::mergeQueryingParamsToRequest($request);
        $records = OrderProduct::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('order_products_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('order-products.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        OrderProduct::mergeQueryingParamsToRequest($request);
        $records = OrderProduct::getRecordsFinalized($request, OrderProduct::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('order_products_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('order-products.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create(Request $request)
    {
        $order = Order::findOrFail($request->input('order_id'));
        $processes = $order->manufacturer->getReadyForOrderProcesses();

        return view('order-products.create', compact('order', 'processes'));
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(OrderProductStoreRequest $request)
    {
        OrderProduct::createFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(OrderProduct $instance)
    {
        $processes = $instance->order->manufacturer->getReadyForOrderProcesses();

        return view('order-products.edit', compact('instance', 'processes'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(OrderProductUpdateRequest $request, OrderProduct $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        OrderProduct::mergeExportQueryingParamsToRequest($request);
        $records = OrderProduct::getRecordsFinalized($request, finaly: 'query');

        return OrderProduct::exportRecordsAsExcel($records);
    }
}
