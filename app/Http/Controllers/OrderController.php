<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Manufacturer;
use App\Models\MarketingAuthorizationHolder;
use App\Models\Order;
use App\Models\Process;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Order::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Order::mergeQueryingParamsToRequest($request);
        $records = Order::getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('orders_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('orders.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Order::mergeQueryingParamsToRequest($request);
        $records = Order::getRecordsFinalized($request, Order::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('orders_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('orders.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('orders.create');
    }

    public function getCreateProductInputs(Request $request)
    {
        $processes = Process::getReadyForOrderRecordsOfManufacturer($request->manufacturer_id, $request->country_code_id);
        $marketingAuthorizationHolders = MarketingAuthorizationHolder::getAll();
        $productIndex = $request->product_index;

        return view('orders.partials.create-products-template', compact('processes', 'marketingAuthorizationHolders', 'productIndex'));
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        Order::createFromRequest($request);

        return to_route('orders.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Order $instance)
    {
        return view('orders.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(OrderUpdateRequest $request, Order $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Export records as excel file
     */
    public function export(Request $request)
    {
        Order::mergeExportQueryingParamsToRequest($request);
        $records = Order::getRecordsFinalized($request, finaly: 'query');

        return Order::exportRecordsAsExcel($records);
    }

    public function confirmedOrders(Request $request)
    {
        Order::mergeExportQueryingParamsToRequest($request);
        $records = Order::where('is_confirmed', true)
            ->withCount('products')
            ->withCount('invoices')
            ->paginate(50);

        $allTableColumns = $request->user()->collectAllTableColumns('confirmed_orders_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('confirmed-orders.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }
}
