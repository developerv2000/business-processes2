<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\Commentable;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use Commentable;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/epp.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/epp';

    protected $guarded = ['id'];

    protected $with = [
        'bdm:id,name,photo',
        'analyst:id,name,photo',
        'country',
        'category',
        'presences',
        'blacklists',
        'productClasses',
        'zones',
        'lastComment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function processes()
    {
        return $this->hasManyThrough(
            Process::class,
            Product::class,
            'manufacturer_id', // Foreign key on Products table
            'product_id',   // Foreign key on Processes table
            'id',         // Local key on Manufacturers table
            'id'          // Local key on Products table
        );
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function bdm()
    {
        return $this->belongsTo(User::class, 'bdm_user_id');
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function category()
    {
        return $this->belongsTo(ManufacturerCategory::class);
    }

    public function presences()
    {
        return $this->hasMany(ManufacturerPresence::class);
    }

    public function blacklists()
    {
        return $this->belongsToMany(ManufacturerBlacklist::class);
    }

    public function productClasses()
    {
        return $this->belongsToMany(ProductClass::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function ($instance) {
            $instance->name = strtoupper($instance->name);
        });

        static::deleting(function ($instance) { // trashing
            foreach ($instance->products as $product) {
                $product->delete();
            }

            foreach ($instance->processes as $process) {
                $process->delete();
            }

            foreach ($instance->meetings as $meeting) {
                $meeting->delete();
            }
        });

        static::restored(function ($instance) {
            foreach ($instance->products()->onlyTrashed()->get() as $product) {
                $product->restore();
            }

            foreach ($instance->processes()->onlyTrashed()->get() as $process) {
                $process->restore();
            }

            foreach ($instance->meetings()->onlyTrashed()->get() as $meeting) {
                $meeting->restore();
            }
        });

        static::forceDeleting(function ($instance) {
            $instance->zones()->detach();
            $instance->productClasses()->detach();
            $instance->blacklists()->detach();

            foreach ($instance->comments as $comment) {
                $comment->delete();
            }

            foreach ($instance->presences as $presence) {
                $presence->delete();
            }

            foreach ($instance->products()->withTrashed()->get() as $product) {
                $product->forceDelete();
            }

            foreach ($instance->processes()->withTrashed()->get() as $process) {
                $process->forceDelete();
            }

            foreach ($instance->meetings()->withTrashed()->get() as $meeting) {
                $meeting->forceDelete();
            }
        });
    }

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
        $whereEqualAttributes = [
            'analyst_user_id',
            'bdm_user_id',
            'country_id',
            'id',
            'category_id',
            'is_active',
            'is_important',
        ];

        $dateRangeAttributes = [
            'created_at',
            'updated_at',
        ];

        $belongsToManyRelations = [
            'productClasses',
            'zones',
            'blacklists',
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterBelongsToManyRelations($request, $query, $belongsToManyRelations);

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

        // attach relationship counts to the query
        $records = $records->withCount('products')
            ->withCount('meetings')
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
     * Get all records minified & ordered by products count
     *
     * Used on filtering and creating/editing of model records
     */
    public static function getAllPrioritizedAndMinifed()
    {
        return self::select('id', 'name')
            ->withOnly([])
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->orderBy('id', 'asc')
            ->get();
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
        $instance->productClasses()->attach($request->input('productClasses'));
        $instance->blacklists()->attach($request->input('blacklists'));

        // HasMany relations
        $instance->storeComment($request->comment);
        $instance->storePresences($request->presences);
    }

    private function storePresences($presences)
    {
        if (!$presences) return;

        foreach ($presences as $name) {
            $this->presences()->create(['name' => $name]);
        }
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // BelongsToMany relations
        $this->zones()->sync($request->input('zones'));
        $this->productClasses()->sync($request->input('productClasses'));
        $this->blacklists()->sync($request->input('blacklists'));

        // HasMany relations
        $this->storeComment($request->comment);
        $this->syncPresences($request->presences);
    }

    private function syncPresences($presences)
    {
        // Remove existing presences if $presences is empty
        if (!$presences) {
            $this->presences()->delete();
            return;
        }

        // Add new presences
        foreach ($presences as $name) {
            if (!$this->presences->contains('name', $name)) {
                $this->presences()->create(['name' => $name]);
            }
        }

        // Delete removed presences
        $this->presences()->whereNotIn('name', $presences)->delete();
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
    public static function getDefaultTableColumns(): array
    {
        $order = 1;

        return [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 144, 'visible' => 1],
            ['name' => 'IVP', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Category', 'order' => $order++, 'width' => 104, 'visible' => 1],
            ['name' => 'Status', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Important', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Product class', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Black list', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Presence', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Website', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'About company', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Relationship', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'Meetings', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
        ];
    }

    /**
     * Return an array of status options
     *
     * used on creating/updating of records as radiogroups
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            (object) ['caption' => trans('Active'), 'value' => 1],
            (object) ['caption' => trans('Stop/pause'), 'value' => 0],
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
            $this->bdm->name,
            $this->analyst->name,
            $this->country->name,
            $this->products_count,
            $this->name,
            $this->category->name,
            $this->is_active ? __('Active') : __('Stoped'),
            $this->is_important ? __('Important') : '',
            $this->productClasses->pluck('name')->implode(' '),
            $this->zones->pluck('name')->implode(' '),
            $this->blacklists->pluck('name')->implode(' '),
            $this->presences->pluck('name')->implode(' '),
            $this->website,
            $this->about,
            $this->relationships,
            $this->comments->pluck('body')->implode(' / '),
            $this->lastComment?->created_at,
            $this->created_at,
            $this->updated_at,
            $this->meetings_count,
        ];
    }
}
