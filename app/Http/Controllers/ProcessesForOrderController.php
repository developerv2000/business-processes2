<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\User;
use App\Support\Helper;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Http\Request;

class ProcessesForOrderController extends Controller
{
    use MergesParamsToRequest;

    const DEFAULT_ORDER_BY = 'readiness_for_order_date';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    public function index(Request $request)
    {
        self::mergeQueryingParamsToRequest($request);
        $records = $this->getRecordsFinalized($request, finaly: 'paginate');

        $allTableColumns = $request->user()->collectAllTableColumns('processes_for_order_table_columns');
        $visibleTableColumns = User::filterOnlyVisibleColumns($allTableColumns);

        return view('processes-for-order.index', compact('request', 'records', 'allTableColumns', 'visibleTableColumns'));
    }

    public function edit(Process $instance)
    {
        return view('processes-for-order.edit', compact('instance'));
    }

    public function update(Request $request, Process $instance)
    {
        $request->validate([
            'fixed_trademark_en_for_order' => ['required', 'string'],
            'fixed_trademark_ru_for_order' => ['required', 'string'],
        ]);

        $instance->fill($request->only([
            'fixed_trademark_en_for_order',
            'fixed_trademark_ru_for_order'
        ]));

        $instance->timestamps = false;
        $instance->saveQuietly();

        return redirect($request->input('previous_url'));
    }

    public function getRecordsFinalized($request, $query = null, $finaly = 'paginate')
    {
        // If no query is provided, create a new query instance
        $query = $query ?: Process::query();
        $query->onlyReadyForOrder();

        $query = $this->filterRecords($request, $query);

        // Get the finalized records based on the specified finaly option
        $records = $this->finalizeRecords($request, $query, $finaly);

        return $records;
    }

    private function filterRecords($request, $query)
    {
        $whereInAttributes = [
            'id',
            'country_code_id',
            'marketing_authorization_holder_id',
            'trademark_en',
            'trademark_ru',
            'fixed_trademark_en_for_order',
            'fixed_trademark_ru_for_order',
        ];

        $dateRangeAttributes = [
            'created_at',
        ];

        $whereRelationLikeStatements = [
            [
                'name' => 'product',
                'attribute' => 'pack',
            ],
        ];

        $whereRelationInStatements = [
            [
                'name' => 'product',
                'attribute' => 'form_id',
            ],
        ];

        $whereRelationInAmbigiousStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'manufacturer_id',
                'ambiguousAttribute' => 'manufacturers.id',
            ],
        ];

        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterWhereRelationLikeStatements($request, $query, $whereRelationLikeStatements);
        $query = Helper::filterWhereRelationInStatements($request, $query, $whereRelationInStatements);
        $query = Helper::filterWhereRelationInAmbigiousStatements($request, $query, $whereRelationInAmbigiousStatements);

        return $query;
    }

    public static function finalizeRecords($request, $query, $finaly)
    {
        // Apply sorting based on request parameters
        $records = $query
            ->orderBy($request->orderBy, $request->orderType)
            ->orderBy('id', $request->orderType);

        // Handle different finaly options
        switch ($finaly) {
            case 'paginate':
                // Paginate the results
                $records = $records
                    ->paginate($request->paginationLimit, ['*'], 'page', $request->page)
                    ->appends($request->except(['page', 'reversedSortingUrl']));
                break;

            case 'get':
                // Retrieve all records without pagination
                $records = $records->get();
                break;

            case 'query':
                // No additional action needed for 'query' option
                break;
        }

        return $records;
    }
}
