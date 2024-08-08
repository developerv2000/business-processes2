<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\HasAttachments;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends CommentableModel
{
    use SoftDeletes;
    use MergesParamsToRequest;
    use ExportsRecords;
    use HasAttachments;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/ivp.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/ivp';

    protected $guarded = ['id'];

    protected $with = [
        'inn',
        'form',
        'shelfLife',
        'class',
        'zones',
        'attachments',
        'lastComment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class)->withTrashed();
    }

    public function processes()
    {
        return $this->hasMany(Process::class)->withTrashed();
    }

    public function untrashedProcesses()
    {
        return $this->hasMany(Process::class);
    }

    public function inn()
    {
        return $this->belongsTo(Inn::class);
    }

    public function form()
    {
        return $this->belongsTo(ProductForm::class, 'form_id');
    }

    public function shelfLife()
    {
        return $this->belongsTo(ProductShelfLife::class);
    }

    public function class()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    public function getProcessesIndexFilteredLinkAttribute()
    {
        return route('processes.index', [
            'manufacturer_id[]' => $this->manufacturer_id,
            'inn_id[]' => $this->inn_id,
            'form_id[]' => $this->form_id,
            'dosage' => $this->dosage,
            'pack' => $this->pack,
        ]);
    }

    public function getCoincidentKvppsAttribute()
    {
        return Kvpp::where([
            'inn_id' => $this->inn_id,
            'form_id' => $this->form_id,
            'dosage' => $this->dosage,
            'pack' => $this->pack,
        ])
            ->select('id', 'country_code_id')
            ->withOnly('country')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function ($item) { // trash
            foreach ($item->untrashedProcesses as $process) {
                $process->delete();
            }
        });

        static::restoring(function ($item) {
            if ($item->manufacturer->trashed()) {
                $item->manufacturer->restoreQuietly();
            }

            foreach ($item->processes as $process) {
                $process->restoreQuietly();
            }
        });

        static::forceDeleting(function ($item) {
            $item->zones()->detach();

            foreach ($item->comments as $comment) {
                $comment->delete();
            }

            foreach ($item->processes as $process) {
                $process->forceDelete();
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
            'manufacturer' => function ($query) {
                $query->select('id', 'name', 'country_id', 'bdm_user_id', 'analyst_user_id', 'category_id')
                    ->withOnly(['country', 'bdm:id,name,photo', 'analyst:id,name,photo', 'category']);
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
            'inn_id',
            'form_id',
            'class_id',
            'shelf_life_id',
            'brand',
            'manufacturer_id',
        ];

        $whereLikeAttributes = [
            'dosage',
            'pack',
        ];

        $dateRangeAttributes = [
            'created_at',
            'updated_at',
        ];

        $belongsToManyRelations = [
            'zones',
        ];

        $whereRelationEqualStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'analyst_user_id',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'bdm_user_id',
            ],
        ];

        $whereRelationInStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'country_id',
            ],
        ];

        $whereRelationEqualAmbigiousStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'manufacturer_category_id',
                'ambiguousAttribute' => 'manufacturers.category_id',
            ],
        ];

        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryLikeStatements($request, $query, $whereLikeAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterBelongsToManyRelations($request, $query, $belongsToManyRelations);
        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);
        $query = Helper::filterWhereRelationInStatements($request, $query, $whereRelationInStatements);
        $query = Helper::filterWhereRelationEqualAmbiguousStatements($request, $query, $whereRelationEqualAmbigiousStatements);

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
        $records = $records->withCount('untrashedProcesses')
            ->withCount('comments');

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

    /**
     * Get similar records based on the provided request data.
     *
     * Used in AJAX requests
     *
     * @param  \Illuminate\Http\Request  $request The request object containing form data.
     * @return \Illuminate\Database\Eloquent\Collection A collection of similar records.
     */
    public static function getSimilarRecords($request)
    {
        // Get the family IDs of the selected form
        $formFamilyIDs = ProductForm::find($request->form_id)->getFamilyIDs();

        // Query similar records based on manufacturer, inn, and form family IDs
        $similarRecords = Product::where('manufacturer_id', $request->manufacturer_id)
            ->where('inn_id', $request->inn_id)
            ->whereIn('form_id', $formFamilyIDs)
            ->get();

        return $similarRecords;
    }

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

    public static function createFromRequest($request)
    {
        $instance = self::create($request->all());

        // BelongsToMany relations
        $instance->zones()->attach($request->input('zones'));

        // HasMany relations
        $instance->storeComment($request->comment);
        $instance->storeAttachments($request);
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // BelongsToMany relations
        $this->zones()->sync($request->input('zones'));

        // HasMany relations
        $this->storeComment($request->comment);
        $this->storeAttachments($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Get default products class id
     * Used on products create
     */
    public static function getDefaultClassID()
    {
        return ProductClass::where('name', 'ЛС')->first()->id;
    }

    /**
     * Get default zone ids
     * Used on products create
     */
    public static function getDefaultZoneIDs()
    {
        $names = ['II'];

        return Zone::whereIn('name', $names)->pluck('id')->all();
    }

    /**
     * Provides the default table columns along with their properties.
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultTableColumns(): array
    {
        $order = 1;

        return [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'Processes', 'order' => $order++, 'width' => 146, 'visible' => 1],
            ['name' => 'Category', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 144, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Generic', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Basic form', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Dosage', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'MOQ', 'order' => $order++, 'width' => 158, 'visible' => 1],
            ['name' => 'Shelf life', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Product class', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Dossier', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Manufacturer Brand', 'order' => $order++, 'width' => 182, 'visible' => 1],
            ['name' => 'Bioequivalence', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Validity period', 'order' => $order++, 'width' => 128, 'visible' => 1],
            ['name' => 'Registered in EU', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Sold in EU', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Down payment', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'KVPP coincidents', 'order' => $order++, 'width' => 146, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
            ['name' => 'Attachments', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Edit attachments', 'order' => $order++, 'width' => 192, 'visible' => 1],
        ];
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
            $this->untrashed_processes_count,
            $this->manufacturer->category->name,
            $this->manufacturer->country->name,
            $this->manufacturer->name,
            $this->inn->name,
            $this->form->name,
            $this->form->parent_name,
            $this->dosage,
            $this->pack,
            $this->moq,
            $this->shelfLife?->name,
            $this->class->name,
            $this->dossier,
            $this->zones->pluck('name')->implode(' '),
            $this->brand,
            $this->bioequivalence,
            $this->validity_period,
            $this->registered_in_eu ? __('Registered') : '',
            $this->sold_in_eu ? __('Sold') : '',
            $this->down_payment,
            $this->comments->pluck('body')->implode(' / '),
            $this->lastComment?->created_at,
            $this->manufacturer->bdm->name,
            $this->manufacturer->analyst->name,
            $this->created_at,
            $this->updated_at,
            $this->coincident_kvpps->count(),
        ];
    }

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return Helper::truncateString($this->manufacturer->name, 50) . ' / ' . Helper::truncateString($this->inn->name, 50);
    }

    public static function getAllUniqueBrands()
    {
        return self::whereNotNull('brand')->distinct()->pluck('brand');
    }
}
