<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\Commentable;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use Commentable;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/ivp.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/ivp';

    const EXCEL_VP_TEMPLATE_STORAGE_PATH = 'app/excel/templates/ivp-vp.xlsx';
    const EXCEL_VP_EXPORT_STORAGE_PATH = 'app/excel/exports/ivp-vp';

    const VP_DEFAULT_COUNTRIES = [
        'KZ',
        'TM',
        'KG',
        'AM',
        'TJ',
        'UZ',
        'GE',
        'MN',
        'RU',
        'AZ',
        'AL',
        'KE',
        'DO',
        'KH',
        'MM',
    ];

    const VP_FIRST_DEFAULT_COUNTRY_COLUMN_INDEX = 'J';
    const VP_LAST_DEFAULT_COUNTRY_COLUMN_INDEX = 'X';
    const VP_TITLES_ROW_INDEX = 2;
    const VP_PRODUCTS_START_ROW_INDEX = 4;

    protected $guarded = ['id'];

    protected $with = [
        'inn',
        'form',
        'shelfLife',
        'class',
        'zones',
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
            'manufacturer_id' => $this->manufacturer_id,
            'inn_id' => $this->inn_id,
            'form_id' => $this->form_id,
            'dosage' => urlencode($this->dosage),
            'pack' => urlencode($this->pack),
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
        $whereEqualAttributes = [
            'inn_id',
            'form_id',
            'manufacturer_id',
            'class_id',
            'shelf_life_id',
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
                'attribute' => 'country_id',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'analyst_user_id',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'bdm_user_id',
            ],
        ];

        $whereRelationEqualAmbigiousStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'manufacturer_category_id',
                'ambiguousAttribute' => 'manufacturers.category_id',
            ],
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterQueryLikeStatements($request, $query, $whereLikeAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterBelongsToManyRelations($request, $query, $belongsToManyRelations);
        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);
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
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // BelongsToMany relations
        $this->zones()->sync($request->input('zones'));

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
            ['name' => 'MOQ', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Shelf life', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Product class', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Dossier', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Manufacturer Brand', 'order' => $order++, 'width' => 182, 'visible' => 1],
            ['name' => 'Bioequivalence', 'order' => $order++, 'width' => 124, 'visible' => 1],
            ['name' => 'Validity period', 'order' => $order++, 'width' => 110, 'visible' => 1],
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
            $this->shelfLife->name,
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

    /**
     * Requires refactoring and optimization !!!
     *
     * Export VP records as an Excel file.
     *
     * This function exports VP records as an Excel file with additional functionality,
     * such as inserting additional country titles and setting cell styles.
     *
     * @param \Illuminate\Database\Query\Builder $query The query builder instance.
     * @param string $manufacturerName The name of the manufacturer.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The response containing the Excel file.
     */
    public static function exportVpRecordsAsExcel($query, $manufacturerName)
    {
        // Load Excel template
        $templatePath = storage_path(self::EXCEL_VP_TEMPLATE_STORAGE_PATH);
        $spreadsheet = IOFactory::load($templatePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get all items
        $allItems = collect();
        $query->chunk(400, function ($chunked) use (&$allItems) {
            $allItems = $allItems->merge($chunked);
        });

        // Append coincident_kvpps manually, so it won`t load many times
        $allItems->each(function ($item) {
            $item->coincident_kvpps = $item->coincident_kvpps;
        });

        // Collect unique additional countries
        $additionalCountries = $allItems->flatMap->coincident_kvpps->pluck('countryCode.name')->unique();

        // Remove countries already present in default countries
        $additionalCountries = $additionalCountries->diff(self::VP_DEFAULT_COUNTRIES);

        // insert additional country titles between last default country and ZONE 4B columns
        $lastCountryColumnIndex = self::VP_LAST_DEFAULT_COUNTRY_COLUMN_INDEX;
        foreach ($additionalCountries as $country) {
            $nextCountryColumnIndex = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($lastCountryColumnIndex) + 1);
            $worksheet->insertNewColumnBefore($nextCountryColumnIndex, 1);
            $insertedCellCoordinates = $nextCountryColumnIndex . self::VP_TITLES_ROW_INDEX;
            $worksheet->setCellValue($insertedCellCoordinates, $country);
            // Update cell styles
            $worksheet->getColumnDimension($nextCountryColumnIndex)->setWidth(5);
            $cellStyle = $worksheet->getCell($insertedCellCoordinates)->getStyle();
            $cellStyle->getFill()->getStartColor()->setARGB('00FFFF');
            $cellStyle->getFont()->setColor(new Color(Color::COLOR_BLACK));
            $lastCountryColumnIndex = $nextCountryColumnIndex;
        }

        // Join default and additional countries
        $allCountries = collect(self::VP_DEFAULT_COUNTRIES)->merge($additionalCountries);

        // Insert product rows
        $rowIndex = self::VP_PRODUCTS_START_ROW_INDEX;
        $productsCounter = 1;

        foreach ($allItems as $item) {
            $worksheet->setCellValue('A' . $rowIndex, $productsCounter);
            $worksheet->setCellValue('B' . $rowIndex, $item->inn->name);
            $worksheet->setCellValue('C' . $rowIndex, $item->form->name);
            $worksheet->setCellValue('D' . $rowIndex, $item->dosage);
            $worksheet->setCellValue('E' . $rowIndex, $item->pack);
            $worksheet->setCellValue('F' . $rowIndex, $item->moq);
            $worksheet->setCellValue('G' . $rowIndex, $item->shelfLife->name);

            $countryColumnIndex = self::VP_FIRST_DEFAULT_COUNTRY_COLUMN_INDEX;  // Reset value for each row
            foreach ($allCountries as $country) {
                $cellIndex = $countryColumnIndex . $rowIndex;
                $cellStyle = $worksheet->getCell($cellIndex)->getStyle();

                if ($item->coincident_kvpps->contains('countryCode.name', $country)) {
                    $worksheet->setCellValue($cellIndex, '1');
                    $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $cellStyle->getFill()->getStartColor()->setARGB('92D050');
                } else {
                    // Reset background color because new inserted rows copy previous row styles
                    $cellStyle->getFill()->getStartColor()->setARGB('FFFFFF');
                }

                $countryColumnIndex = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($countryColumnIndex) + 1);
            }

            $rowIndex++;
            $productsCounter++;
            $worksheet->insertNewRowBefore($rowIndex, 1);  // Insert new rows to escape rewriting default countries list
        }

        // Remove last inserted row
        if ($allItems->isNotEmpty()) {
            $worksheet->removeRow($rowIndex);
        }

        // Save file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = $manufacturerName . date(' Y-m-d') . '.xlsx';
        $filename = Helper::escapeDuplicateFilename($filename, storage_path(self::EXCEL_VP_EXPORT_STORAGE_PATH));
        $filePath = storage_path(self::EXCEL_VP_EXPORT_STORAGE_PATH  . '/' . $filename);
        $writer->save($filePath);

        return response()->download($filePath);
    }
}
