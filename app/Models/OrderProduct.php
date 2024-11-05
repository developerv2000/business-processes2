<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class OrderProduct extends CommentableModel
{
    use SoftDeletes;
    use MergesParamsToRequest;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/order-products.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/order-products';

    protected $guarded = ['id'];

    public $with = [
        'marketingAuthorizationHolder',
        'lastComment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function process()
    {
        return $this->belongsTo(Process::class)->withTrashed();
    }

    public function marketingAuthorizationHolder()
    {
        return $this->belongsTo(MarketingAuthorizationHolder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    public function getTotalPriceAttribute()
    {
        return round($this->quantity * $this->price, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::restoring(function ($instance) {
            if ($instance->order->trashed()) {
                $instance->order->restoreQuietly();
            }
        });

        static::forceDeleting(function ($instance) {
            foreach ($instance->comments as $comment) {
                $comment->delete();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

    /**
     * Scoping queries with eager loaded complex relationships
     */
    public function scopeWithComplexRelations($query)
    {
        return $query->with([
            'order' => function ($ordersQuery) {
                $ordersQuery->select('*')
                    ->withOnly([
                        'country',
                        'currency',
                        'manufacturer' => function ($manufacturersQuery) {
                            $manufacturersQuery->select('manufacturers.id', 'manufacturers.name')
                                ->withOnly([]);
                        },
                    ]);
            },
            'process' => function ($processQuery) {
                $processQuery->select('processes.id', 'processes.fixed_trademark_en_for_order', 'processes.fixed_trademark_ru_for_order')
                    ->withOnly([]);
            }
        ]);
    }

    /**
     * Get finalized records based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Query\Builder|null $query
     * @param string $finaly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function getRecordsFinalized($request, $query = null, $finaly = 'paginate')
    {
        // If no query is provided, create a new query instance
        $query = $query ?: self::query();

        $query = self::filterRecords($request, $query);

        // Get the finalized records based on the specified finaly option
        $records = self::finalizeRecords($request, $query, $finaly);

        return $records;
    }

    private static function filterRecords($request, $query)
    {
        $whereInAttributes = [
            'id',
            'order_id',
            'marketing_authorization_holder_id',
        ];

        $whereEqualAttributes = [
            'quantity',
            'price',
        ];

        $dateRangeAttributes = [
            'created_at',
            'updated_at',
        ];

        $whereRelationInStatements = [
            [
                'name' => 'order',
                'attribute' => 'manufacturer_id',
            ],

            [
                'name' => 'order',
                'attribute' => 'country_code_id',
            ],

            [
                'name' => 'order',
                'attribute' => 'purchase_order_name',
            ],

            [
                'name' => 'process',
                'attribute' => 'fixed_trademark_en_for_order',
            ],

            [
                'name' => 'process',
                'attribute' => 'fixed_trademark_ru_for_order',
            ],
        ];

        $whereRelationEqualStatements = [
            [
                'name' => 'order',
                'attribute' => 'currency_id',
            ],

            [
                'name' => 'order',
                'attribute' => 'is_confirmed',
            ],
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);
        $query = Helper::filterWhereRelationInStatements($request, $query, $whereRelationInStatements);

        return $query;
    }

    /**
     * Finalize the query based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $finaly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function finalizeRecords($request, $query, $finaly)
    {
        // Apply sorting based on request parameters
        $records = $query
            ->orderBy($request->orderBy, $request->orderType)
            ->orderBy('id', $request->orderType);

        // eager load complex relations
        $records = $records->withComplexRelations();

        // with counts
        $records->withCount('comments');

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

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

    public static function createFromRequest($request)
    {
        $instance = self::create($request->all());

        // HasMany relations
        $instance->storeComment($request->comment);
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // HasMany relations
        $this->storeComment($request->comment);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Provides the default table columns along with their properties.
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultTableColumnsForUser($user)
    {
        if (Gate::forUser($user)->denies('view-orders')) {
            return null;
        }

        $order = 1;
        $columns = array();

        if (Gate::forUser($user)->allows('edit-orders')) {
            array_push(
                $columns,
                ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'Receive date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'PO date', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'PO №', 'order' => $order++, 'width' => 122, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand name ENG', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'Brand name RUS', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Quantity', 'order' => $order++, 'width' => 90, 'visible' => 1],
            ['name' => 'Price', 'order' => $order++, 'width' => 80, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Sum', 'order' => $order++, 'width' => 80, 'visible' => 1],
            ['name' => 'Readiness date', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Mfg lead time', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Expected dispatch date', 'order' => $order++, 'width' => 184, 'visible' => 1],
            ['name' => 'Confirmed', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
        );

        return $columns;
    }

    /**
     * Get the Excel column values for exporting.
     *
     * This function returns an array containing the values of specific properties
     * of the current model instance, which are intended to be exported to an Excel file.
     *
     * @return array An array containing the Excel column values.
     */
    public function getExcelColumnValuesForExport()
    {
        return [
            $this->id,
            $this->order->receive_date?->isoFormat('YYYY-MM-DD'),
            $this->order->purchase_order_date?->isoFormat('YYYY-MM-DD'),
            $this->order->purchase_order_name,
            $this->order->manufacturer->name,
            $this->order->country->name,
            $this->process->fixed_trademark_en_for_order,
            $this->process->fixed_trademark_ru_for_order,
            $this->marketingAuthorizationHolder->name,
            $this->quantity,
            $this->price,
            $this->order->currency->name,
            $this->total_price,
            $this->order->readiness_date?->isoFormat('YYYY-MM-DD'),
            $this->order->lead_time,
            $this->order->expected_dispatch_date?->isoFormat('YYYY-MM-DD'),
            $this->order->is_confirmed ? 'Confirmed' : '',
            $this->comments->pluck('body')->implode(' / '),
            $this->lastComment?->created_at,
            $this->created_at,
            $this->updated_at,
        ];
    }

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return $this->process->fixed_trademark_en_for_order;
    }
}
