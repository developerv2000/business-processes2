<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Order extends CommentableModel
{
    use SoftDeletes;
    use MergesParamsToRequest;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/orders.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/orders';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void {}

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */

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
            'purchase_order_name',
        ];

        $whereEqualAttributes = [
            'manufacturer_id',
            'currency_id',
            'is_confirmed',
        ];

        $dateRangeAttributes = [
            'created_at',
            'updated_at',
            'receive_date',
            'purchase_order_date',
            'readiness_date',
            'expected_dispatch_date',
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);

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

        // with counts
        $records = $records->withCount('products');

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
        return self::create($request->all());
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());
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
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 92, 'visible' => 1],
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
            $this->year,
            $this->manufacturer->name,
            $this->manufacturer->bdm->name,
            $this->manufacturer->analyst->name,
            $this->manufacturer->country->name,
            $this->who_met,
            $this->plan,
            $this->topic,
            $this->result,
            $this->outside_the_exhibition,
            $this->created_at,
            $this->updated_at,
        ];
    }

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return '#' . $this->id . ' / ' . $this->purchase_order_name;
    }
}
