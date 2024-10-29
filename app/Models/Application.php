<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Application extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/applications.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/applications';

    protected $guarded = ['id'];

    protected $with = [];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function ($item) { // trash
            // foreach ($item->untrashedProcesses as $process) {
            //     $process->delete();
            // }
        });

        static::restoring(function ($item) {
            // foreach ($item->processes as $process) {
            //     $process->restoreQuietly();
            // }
        });

        static::forceDeleting(function ($item) {
            // foreach ($item->processes as $process) {
            //     $process->forceDelete();
            // }
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
            'process' => function ($query) {
                $query->select(
                    'id',
                    'product_id',
                    'country_code_id',
                    'marketing_authorization_holder_id',
                    'trademark_en',
                    'trademark_ru',
                )
                    ->withOnly([
                        'searchCountry',
                        'marketingAuthorizationHolder',

                        'product' => function ($productsQuery) {
                            $productsQuery->select('products.id', 'manufacturer_id')
                                ->withOnly([]);
                        },

                        'manufacturer' => function ($manufacturersQuery) {
                            $manufacturersQuery->select('manufacturers.id', 'manufacturers.name')
                                ->withOnly([]);
                        },
                    ]);
            },
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
        ];

        $dateRangeAttributes = [
            'updated_at',
            'created_at',
        ];

        $whereRelationInStatements = [
            [
                'name' => 'process.searchCountry',
                'attribute' => 'country_code_id',
            ],

            [
                'name' => 'process.marketingAuthorizationHolder',
                'attribute' => 'marketing_authorization_holder_id',
            ],

            [
                'name' => 'process',
                'attribute' => 'trademark_en',
            ],

            [
                'name' => 'process',
                'attribute' => 'trademark_ru',
            ],
        ];

        $whereRelationInAmbigiousStatements = [
            [
                'name' => 'process.manufacturer',
                'attribute' => 'manufacturer_id',
                'ambiguousAttribute' => 'manufacturers.id',
            ],
        ];

        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterWhereRelationInStatements($request, $query, $whereRelationInStatements);
        $query = Helper::filterWhereRelationInAmbigiousStatements($request, $query, $whereRelationInAmbigiousStatements);

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

        // attach relationship counts to the query
        $records = $records->withCount('orders');

        // Handle different finaly options
        switch ($finaly) {
            case 'paginate':
                // Paginate the results
                $records = $records
                    ->paginate($request->paginationLimit, ['*'], 'page', $request->page)
                    ->appends($request->except(['page', 'reversedSortingUrl']));

                self::syncManufacturersForRecords($records);
                break;

            case 'get':
                // Retrieve all records without pagination
                $records = $records->get();

                self::syncManufacturersForRecords($records);
                break;

            case 'query':
                // No additional action needed for 'query' option
                break;
        }

        return $records;
    }

    public static function getAllMinified()
    {
        return self::select('id', 'name')->withOnly([])->orderBy('name')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

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
     * Syncs the manufacturer attribute on each record for filtering compatibility.
     *
     * Note: Ensure $process->manufacturer is eager loaded.
     *
     * @param \Illuminate\Support\Collection|array $records Collection or array of records.
     * @return void
     */
    public static function syncManufacturersForRecords($records)
    {
        foreach ($records as $record) {
            $record->manufacturer = $record->process->manufacturer ?? null;
        }
    }

    /**
     * Provides the default table columns along with their properties.
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultTableColumnsForUser($user): array
    {
        $order = 1;
        $columns = array();

        if (Gate::forUser($user)->allows('edit-applications')) {
            array_push(
                $columns,
                ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'ID', 'order' => $order++, 'width' => 60, 'visible' => 1],
            ['name' => 'PO №', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Orders', 'order' => $order++, 'width' => 86, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand Eng', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Brand Rus', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 144, 'visible' => 1],
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
            $this->name,
            $this->process->manufacturer->name,
            $this->process->searchCountry->name,
            $this->process->trademark_en,
            $this->process->trademark_ru,
            $this->process->marketingAuthorizationHolder?->name,
            $this->created_at,
        ];
    }

    public static function pluckAllEnTrademarks()
    {
        return Process::whereIn('id', function ($query) {
            $query->select('process_id')
                ->from('applications');
        })->pluck('trademark_en');
    }

    public static function pluckAllRuTrademarks() {
        return Process::whereIn('id', function ($query) {
            $query->select('process_id')
                ->from('applications');
        })->pluck('trademark_ru');
    }
}
