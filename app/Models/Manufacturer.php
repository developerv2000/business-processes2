<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\Commentable;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Manufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use Commentable;

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
     * Export items to Excel file.
     *
     * This function exports the given items to an Excel file using a template.
     * It saves the Excel file to storage and returns a download response.
     *
     * @param \Illuminate\Support\Collection $items The items to export.
     * @return \Illuminate\Http\Response The download response.
     */
    public static function exportItemsAsExcel($items)
    {
        // Load the Excel template
        $template = storage_path(self::EXCEL_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        // Start adding items from first column and second row (A2)
        $columnIndex = 1;
        $row = 2;

        // Chunk items to avoid memory issues and iterate over each chunk
        $items->chunk(800, function ($itemsChunk) use (&$sheet, &$columnIndex, &$row) {
            foreach ($itemsChunk as $instance) {
                $columnIndex = 1;
                $sheet->setCellValue([$columnIndex++, $row], $instance->name);
                $sheet->setCellValue([$columnIndex++, $row], $instance->category->name);

                // Increment row for next item
                $row++;
            }
        });

        // Save the Excel file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = date('Y-m-d H-i-s') . '.xlsx';
        $filename = Helper::escapeDuplicateFilename($filename, storage_path(self::EXCEL_EXPORT_STORAGE_PATH));
        $filePath = storage_path(self::EXCEL_EXPORT_STORAGE_PATH  . '/' . $filename);
        $writer->save($filePath);

        // Return download response
        return response()->download($filePath);
    }
}
