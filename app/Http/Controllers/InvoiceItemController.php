<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\User;
use App\Support\Traits\DestroysModelRecords;
use App\Support\Traits\RestoresModelRecords;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    use DestroysModelRecords;
    use RestoresModelRecords;

    public $model = InvoiceItem::class; // used in multiple destroy/restore traits

    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        InvoiceItem::mergeQueryingParamsToRequest($request);
        $records = InvoiceItem::getRecordsFinalized($request, finaly: 'paginate');

        return view('invoice-items.index', compact('request', 'records'));
    }

    /**
     * Display a listing of the trashed records.
     */
    public function trash(Request $request)
    {
        InvoiceItem::mergeQueryingParamsToRequest($request);
        $records = InvoiceItem::getRecordsFinalized($request, InvoiceItem::onlyTrashed(), finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('invoice-items_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('invoice-items.trash', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('invoice-items.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(Request $request)
    {
        InvoiceItem::createFromRequest($request);

        return to_route('invoice-items.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(InvoiceItem $instance)
    {
        return view('invoice-items.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(Request $request, InvoiceItem $instance)
    {
        $instance->updateFromRequest($request);

        return redirect($request->input('previous_url'));
    }
}
