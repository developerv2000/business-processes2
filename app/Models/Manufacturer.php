<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\Commentable;
use App\Support\Traits\ExportsItems;
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
    use ExportsItems;

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
            // foreach ($instance->meetings as $meeting) {
            //     $meeting->delete();
            // }

            // foreach ($instance->generics as $generic) {
            //     $generic->delete();
            // }

            // foreach ($instance->processes as $process) {
            //     $process->delete();
            // }
        });

        static::restored(function ($instance) {
            // foreach ($instance->meetings()->onlyTrashed()->get() as $meeting) {
            //     $meeting->restore();
            // }

            // foreach ($instance->generics()->onlyTrashed()->get() as $generic) {
            //     $generic->restore();
            // }

            // foreach ($instance->processes()->onlyTrashed()->get() as $process) {
            //     $process->restore();
            // }
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

            // foreach ($instance->meetings()->withTrashed()->get() as $meeting) {
            //     $meeting->forceDelete();
            // }

            // foreach ($instance->generics()->withTrashed()->get() as $generic) {
            //     $generic->forceDelete();
            // }

            // foreach ($instance->processes()->withTrashed()->get() as $process) {
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
     * Get finalized items based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Query\Builder|null $query
     * @param string $finaly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function getItemsFinalized($request, $query = null, $finaly = 'paginate')
    {
        // If no query is provided, create a new query instance
        $query = $query ?: self::query();

        $query = self::filterItems($request, $query);

        // Get the finalized items based on the specified finaly option
        $items = self::finalizeItems($request, $query, $finaly);

        return $items;
    }

    private static function filterItems($request, $query)
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
    public static function finalizeItems($request, $query, $finaly)
    {
        // Apply sorting based on request parameters
        $items = $query
            ->orderBy($request->orderBy, $request->orderType)
            ->orderBy('id', $request->orderType);

        // Handle different finaly options
        switch ($finaly) {
            case 'paginate':
                // Paginate the results
                $items = $items
                    ->paginate($request->paginationLimit, ['*'], 'page', $request->page)
                    ->appends($request->except(['page', 'reversedSortingUrl']));
                break;

            case 'get':
                // Retrieve all items without pagination
                $items = $items->get();
                break;

            case 'query':
                // No additional action needed for 'query' option
                break;
        }

        return $items;
    }

    public static function getAllMinifed()
    {
        return self::select('id', 'name')->withOnly([])->get();
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
     * Return an array of status options
     *
     * used on creating/updating of items as radiogroups
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
            'NOT DONE YET!!!',  // ИВП
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
            'NOT DONE YET!!!', // Встречи
        ];
    }
}
