<?php

namespace App\Models;

use App\Support\Abstracts\CommentableModel;
use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends CommentableModel
{
    use SoftDeletes;
    use MergesParamsToRequest;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/vps.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/vps';

    protected $guarded = ['id'];

    protected $with = [
        'searchCountry',
        'status',
        'currency',
        'marketingAuthorizationHolder',
        'clinicalTrialCountries',
        'responsiblePeople',
        'lastComment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_update_date' => 'date',
            'forecast_year_1_update_date' => 'date',
            'increased_price_date' => 'date',
            'responsible_people_update_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(ProcessStatus::class, 'status_id');
    }

    public function manufacturer()
    {
        return $this->hasOneThrough(
            Manufacturer::class,
            Product::class,
            'id', // Foreign key on the Products table
            'id', // Foreign key on the Manufacturers table
            'product_id', // Local key on the Processes table
            'manufacturer_id' // Local key on the Products table
        )->withTrashedParents()->withTrashed();
    }

    public function searchCountry()
    {
        return $this->belongsTo(CountryCode::class, 'country_code_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function marketingAuthorizationHolder()
    {
        return $this->belongsTo(MarketingAuthorizationHolder::class);
    }

    public function clinicalTrialCountries()
    {
        return $this->belongsToMany(
            Country::class,
            'clinical_trial_country_process',
            'process_id',
            'country_id'
        );
    }

    public function responsiblePeople()
    {
        return $this->belongsToMany(
            ProcessResponsiblePerson::class,
            'process_process_responsible_people',
            'process_id',
            'responsible_person_id'
        );
    }

    public function statusHistory()
    {
        return $this->hasMany(ProcessStatusHistory::class);
    }

    public function currentStatusHistory()
    {
        return $this->hasOne(ProcessStatusHistory::class)
            ->whereNull('end_date')
            ->orderBy('id', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Get the number of days past since the 'responsible_people_update_date'.
     *
     * @return int|null Number of days past since the update date, or null if the date is not set.
     */
    public function getDaysPastAttribute()
    {
        if ($this->responsible_people_update_date) {
            return (int) $this->responsible_people_update_date->diffInDays(now(), false);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function ($instance) {
            $instance->status_update_date = now();

            // set as now() on creating, because responsible people field is required from stage 1
            $instance->responsible_people_update_date = now();
        });

        static::created(function ($instance) {
            $instance->validateManufacturerPriceInUSD();
            $instance->createNewStatusHistory();
        });

        static::updating(function ($instance) {
            // Close the current status history and create a new one, if the status has changed
            if ($instance->isDirty('status_id')) {
                $instance->currentStatusHistory->close();
                $instance->createNewStatusHistory();
            }

            $instance->validateResponsiblePeopleUpdateDate();
        });

        static::updated(function ($instance) {
            $instance->validateManufacturerPriceInUSD();
        });

        static::saving(function ($instance) {
            $instance->validateManufacturerFollowedPrice();
            $instance->syncRelatedProductUpdates();
            $instance->validateForecastUpdateDate();
            $instance->validateIncreasedPrice();
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
            'product' => function ($query) {
                $query->select('id', 'manufacturer_id', 'inn_id', 'class_id', 'form_id', 'dosage', 'pack', 'moq', 'shelf_life_id')
                    ->withOnly(['inn', 'class', 'form', 'shelfLife', 'zones']);
            },

            'manufacturer' => function ($query) {
                $query->select('manufacturers.id', 'manufacturers.name', 'manufacturers.category_id', 'manufacturers.country_id', 'manufacturers.bdm_user_id', 'manufacturers.analyst_user_id')
                    ->withOnly([
                        'category',
                        'country',
                        'bdm' => function ($query) {
                            $query->select('id', 'name', 'photo')
                                ->withOnly([]);
                        },
                        'analyst' => function ($query) {
                            $query->select('id', 'name', 'photo')
                                ->withOnly([]);
                        },
                    ]);
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
            'id',
            'country_code_id',
            'status_id',
            'marketing_authorization_holder_id',
        ];

        $dateRangeAttributes = [
            'status_update_date',
            'created_at',
            'updated_at',
        ];

        $whereLikeAttributes = [
            'trademark_en',
            'trademark_ru',
        ];

        $belongsToManyRelations = [
            'responsiblePeople',
        ];

        $whereRelationEqualStatements = [
            [
                'name' => 'product',
                'attribute' => 'inn_id',
            ],

            [
                'name' => 'product',
                'attribute' => 'form_id',
            ],

            [
                'name' => 'product',
                'attribute' => 'dosage',
            ],

            [
                'name' => 'product',
                'attribute' => 'pack',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'analyst_user_id',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'bdm_user_id',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'country_id',
            ],
        ];

        $whereRelationEqualAmbigiousStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'manufacturer_id',
                'ambiguousAttribute' => 'manufacturers.id',
            ],

            [
                'name' => 'product',
                'attribute' => 'product_class_id',
                'ambiguousAttribute' => 'products.class_id',
            ],

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
        $records = $records->withCount('comments');

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

    /**
     * Create multiple instances of the model from the request data.
     *
     * This method processes an array of country code IDs from the request,
     * merges specific forecast year data for each country code, and creates
     * model instances with the combined data. It also attaches related
     * clinical trial countries and responsible people, and stores comments.
     *
     * @param App\Http\Requests\ProcessStoreRequest $request The request containing the input data.
     * @return void
     */
    public static function createFromRequest($request)
    {
        $countryCodeIDs = $request->input('country_code_ids');

        foreach ($countryCodeIDs as $countryCodeID) {
            $countryCode = CountryCode::find($countryCodeID);

            // Merge additional forecast data for the specific country code into the request array
            $mergedData = $request->merge([
                'country_code_id' => $countryCodeID,
                'forecast_year_1' => $request->input('forecast_year_1_' . $countryCode->name),
                'forecast_year_2' => $request->input('forecast_year_2_' . $countryCode->name),
                'forecast_year_3' => $request->input('forecast_year_3_' . $countryCode->name),
            ])->all();

            $instance = self::create($mergedData);

            // BelongsToMany relations
            $instance->clinicalTrialCountries()->attach($request->input('clinicalTrialCountries'));
            $instance->responsiblePeople()->attach($request->input('responsiblePeople'));

            // HasMany relations
            $instance->storeComment($request->comment);
        }
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

        // BelongsToMany relations
        $this->clinicalTrialCountries()->sync($request->input('clinicalTrialCountries'));
        $this->responsiblePeople()->sync($request->input('responsiblePeople'));

        // HasMany relations
        $this->storeComment($request->comment);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Synchronize related product attributes of the process
     * on the saving event of the model instance.
     */
    private function syncRelatedProductUpdates()
    {
        $product = Product::find($this->product_id);

        // Shelf life and MOQ are available from stage 2
        if (request()->has('shelf_life_id')) {
            $product->shelf_life_id = request()->input('shelf_life_id');
            $product->moq = request()->input('moq');
        }

        // Product class is available only at stages 1 and 2
        if (request()->has('class_id')) {
            $product->class_id = request()->input('class_id');
        }

        if ($product->isDirty()) {
            $product->save();
        }
    }

    /**
     * Validate and set the manufacturer_followed_offered_price attribute
     * on the saving event of the model instance.
     */
    private function validateManufacturerFollowedPrice()
    {
        $firstOfferedPrice = $this->manufacturer_first_offered_price;
        $followedOfferedPrice = $this->manufacturer_followed_offered_price;

        if ($firstOfferedPrice && !$followedOfferedPrice) {
            $this->manufacturer_followed_offered_price = $firstOfferedPrice;
        }
    }

    /**
     * Validate and update the forecast_year_1_update_date attribute
     * on the saving event of the model instance.
     */
    private function validateForecastUpdateDate()
    {
        // forecast_year_1 is available from stage 2
        if ($this->forecast_year_1 && $this->isDirty('forecast_year_1')) {
            $this->forecast_year_1_update_date = now();
        }
    }

    /**
     * Validate and update the increased_price, increased_price_percentage,
     * and increased_price_date attributes on the saving event of the model instance.
     */
    private function validateIncreasedPrice()
    {
        // increased_price is available from stage 4
        if (!$this->increased_price) {
            // If increased_price is not set, reset the percentage and date attributes
            $this->increased_price_percentage = null;
            $this->increased_price_date = null;
        } elseif ($this->isDirty('increased_price')) {
            // If increased_price is set and has been modified, calculate the percentage and update the date
            $this->increased_price_percentage = round(($this->increased_price * 100) / $this->agreed_price, 2);
            $this->increased_price_date = now();
        }
    }

    /**
     * Validate and update the responsible_people_update_date attribute
     * on the updating event of the model instance.
     */
    private function validateResponsiblePeopleUpdateDate()
    {
        // Compare the current responsible people IDs with the requested responsible people IDs
        $requestedIDs = request()->input('responsiblePeople', []);
        $instanceIDs = $this->responsiblePeople->pluck('id')->toArray();

        // Check for differences between the current and requested responsible people IDs
        if (count(array_diff($requestedIDs, $instanceIDs)) || count(array_diff($instanceIDs, $requestedIDs))) {
            // If there are any differences, update the responsible_people_update_date to the current date and time
            $this->responsible_people_update_date = now();
        }
    }

    /**
     * Validate and update the manufacturer_followed_offered_price_in_usd attribute
     * on the created and updated events of the model instance.
     *
     * Timestamps are temporarily disabled because the price in USD is updated daily via cron.
     */
    public function validateManufacturerPriceInUSD()
    {
        // Refresh the instance to get the latest values from the database
        $instance = $this->fresh();

        $followedOfferedPrice = $instance->manufacturer_followed_offered_price;
        $currencyName = $instance->currency?->name;

        // If the followed offered price is set, convert it to USD and update the attribute
        if ($followedOfferedPrice) {
            $convertedPrice = Currency::convertPriceToUSD($followedOfferedPrice, $currencyName);

            // Temporarily disable timestamps to avoid updating the updated_at column
            $instance->timestamps = false;

            // Update the followed offered price in USD and save the instance quietly (without triggering events)
            $instance->manufacturer_followed_offered_price_in_usd = $convertedPrice;
            $instance->saveQuietly();

            $instance->timestamps = true;
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
    public static function getDefaultTableColumns(): array
    {
        $order = 1;

        $columns = [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'Status date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Search country', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Product status', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'Product status An*', 'order' => $order++, 'width' => 136, 'visible' => 1],
            ['name' => 'General status', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Manufacturer category', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Manufacturer country', 'order' => $order++, 'width' => 174, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Generic', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Dosage', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],

            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],

            ['name' => 'Manufacturer price 1', 'order' => $order++, 'width' => 164, 'visible' => 1],
            ['name' => 'Manufacturer price 2', 'order' => $order++, 'width' => 166, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Price in USD', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'Agreed price', 'order' => $order++, 'width' => 114, 'visible' => 1],
            ['name' => 'Our price 2', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Our price 1', 'order' => $order++, 'width' => 118, 'visible' => 1],
            ['name' => 'Increased price', 'order' => $order++, 'width' => 158, 'visible' => 1],
            ['name' => 'Increased price %', 'order' => $order++, 'width' => 172, 'visible' => 1],
            ['name' => 'Increased price date', 'order' => $order++, 'width' => 164, 'visible' => 1],

            ['name' => 'Shelf life', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'MOQ', 'order' => $order++, 'width' => 140, 'visible' => 1],

            ['name' => 'Dossier status', 'order' => $order++, 'width' => 124, 'visible' => 1],
            ['name' => 'Year Cr/Be', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Countries Cr/Be', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Country ich', 'order' => $order++, 'width' => 108, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Down payment 1', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Down payment 2', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Down payment condition', 'order' => $order++, 'width' => 194, 'visible' => 1],

            ['name' => 'Date of forecast', 'order' => $order++, 'width' => 136, 'visible' => 1],
            ['name' => 'Forecast 1 year', 'order' => $order++, 'width' => 148, 'visible' => 1],
            ['name' => 'Forecast 2 year', 'order' => $order++, 'width' => 148, 'visible' => 1],
            ['name' => 'Forecast 3 year', 'order' => $order++, 'width' => 148, 'visible' => 1],

            ['name' => 'Responsible', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Responsible date', 'order' => $order++, 'width' => 250, 'visible' => 1],
            ['name' => 'Days have passed', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Brand Eng', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand Rus', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'Product class', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],

            ['name' => 'ВП', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'ПО', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'АЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'СЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Кк', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'КД', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'НПР', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Р', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Зя', 'order' => $order++, 'width' => 200, 'visible' => 1],
        ];

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
            $this->AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA,
            $this->created_at,
            $this->updated_at,
        ];
    }

    /**
     * Create the initial status history record when the Process is created.
     *
     * @return void
     */
    public function createNewStatusHistory()
    {
        $this->statusHistory()->create([
            'status_id' => $this->status_id,
            'start_date' => now(),
        ]);
    }

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return 'NOT DONE YET!!!';
    }
}
