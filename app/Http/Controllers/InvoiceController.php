<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = Invoice::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        Invoice::mergeQueryingParamsToRequest($request);
        $records = Invoice::getRecordsFinalized($request, finaly: 'paginate');

        return view('invoices.index', compact('request', 'records'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        Invoice::mergeQueryingParamsToRequest($request);
        $records = Invoice::getRecordsFinalized($request, Invoice::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('invoices_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('invoices.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function createGoods()
    {
        return view('invoices.create.goods');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(invoicestoreRequest $request)
    {
        Invoice::createFromRequest($request);

        return to_route('invoices.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(Invoice $instance)
    {
        return view('invoices.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(InvoiceUpdateRequest $request, Invoice $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }
}
