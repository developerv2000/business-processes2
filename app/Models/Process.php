<?php

namespace App\Models;

use App\Notifications\ProcessMarkedAsReadyForOrder;
use App\Notifications\ProcessOnContractStage;
use App\Support\Abstracts\CommentableModel;
use App\Support\Contracts\PreparesRecordsForExportInterface;
use App\Support\Helper;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Process extends CommentableModel implements PreparesRecordsForExportInterface
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
            'status_update_date' => 'datetime',
            'forecast_year_1_update_date' => 'date',
            'increased_price_date' => 'date',
            'responsible_people_update_date' => 'date',
            'readiness_for_order_date' => 'date',
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

    public function orders()
    {
        return $this->hasMany(Order::class);
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
            return round($this->responsible_people_update_date->diffInDays(now(), true));
        }

        return null;
    }

    public function getCoincidentKvppsAttribute()
    {
        return Kvpp::where([
            'inn_id' => $this->product->inn_id,
            'form_id' => $this->product->form_id,
            'dosage' => $this->product->dosage,
            'country_code_id' => $this->country_code_id,
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
        static::creating(function ($instance) {
            $instance->validateStatusUpdateDateOnCreating();
        });

        static::created(function ($instance) {
            $instance->validateManufacturerPriceInUSD();
            $instance->createNewStatusHistory();
        });

        static::updating(function ($instance) {
            $instance->handleStatusUpdate();
            $instance->notifyAdminsOnStatusUpdate();
        });

        static::updated(function ($instance) {
            $instance->validateManufacturerPriceInUSD();
        });

        static::saving(function ($instance) {
            $instance->trademark_en = $instance->trademark_en ? strtoupper($instance->trademark_en) : null;
            $instance->trademark_ru = $instance->trademark_ru ? strtoupper($instance->trademark_ru) : null;
            $instance->syncRelatedProductUpdates();
            $instance->validateForecastUpdateDate();
            $instance->validateIncreasedPrice();
            $instance->validateResponsiblePeopleUpdateDate();
        });

        static::restoring(function ($instance) {
            if ($instance->product->trashed()) {
                $instance->product->restoreQuietly();
            }

            if ($instance->manufacturer->trashed()) {
                $instance->manufacturer->restoreQuietly();
            }
        });

        static::forceDeleting(function ($instance) {
            $instance->responsiblePeople()->detach();
            $instance->clinicalTrialCountries()->detach();

            foreach ($instance->comments as $comment) {
                $comment->delete();
            }

            foreach ($instance->statusHistory as $history) {
                $history->delete();
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
            },
        ]);
    }

    public function scopeOnlyReadyForOrder($query)
    {
        $query->where('is_ready_for_order', true)
            ->select(
                'id',
                'product_id',
                'country_code_id',
                'marketing_authorization_holder_id',
                'trademark_en',
                'trademark_ru',
                'readiness_for_order_date',
                'fixed_trademark_en_for_order',
                'fixed_trademark_ru_for_order',
            )
            ->withOnly([
                'searchCountry',
                'marketingAuthorizationHolder',

                'product' => function ($productsQuery) {
                    $productsQuery->select('products.id', 'manufacturer_id', 'form_id', 'pack')
                        ->withOnly(['form']);
                },

                'manufacturer' => function ($manufacturersQuery) {
                    $manufacturersQuery->select('manufacturers.id', 'manufacturers.name')
                        ->withOnly([]);
                },
            ])
            ->withCount('orders');
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
        ];

        $whereRelationEqualAmbigiousStatements = [
            [
                'name' => 'manufacturer',
                'attribute' => 'manufacturer_category_id',
                'ambiguousAttribute' => 'manufacturers.category_id',
            ],
        ];

        $whereRelationInStatements = [
            [
                'name' => 'status.generalStatus',
                'attribute' => 'name_for_analysts',
            ],

            [
                'name' => 'status',
                'attribute' => 'general_status_id',
            ],

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
                'attribute' => 'brand',
            ],

            [
                'name' => 'manufacturer',
                'attribute' => 'country_id',
            ],
        ];

        $whereRelationInAmbigiousStatements = [
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
        ];

        // Templated filtering
        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
        $query = Helper::filterQueryLikeStatements($request, $query, $whereLikeAttributes);
        $query = Helper::filterQueryDateRangeStatements($request, $query, $dateRangeAttributes);
        $query = Helper::filterBelongsToManyRelations($request, $query, $belongsToManyRelations);
        $query = Helper::filterWhereRelationEqualStatements($request, $query, $whereRelationEqualStatements);
        $query = Helper::filterWhereRelationEqualAmbiguousStatements($request, $query, $whereRelationEqualAmbigiousStatements);
        $query = Helper::filterWhereRelationInStatements($request, $query, $whereRelationInStatements);
        $query = Helper::filterWhereRelationInAmbigiousStatements($request, $query, $whereRelationInAmbigiousStatements);

        // Additional specific filters
        $query = self::filterRecordsByRoles($request, $query);
        $query = self::filterSpecificManufacturerCountry($request, $query);

        // If redirected from statitics index page & stage 5 (Kk) requested (Table 1)
        if ($request->contracted_on_requested_month_and_year) {
            $query = self::filterRecordsContractedOnRequestedMonthAndYear($query, $request->contracted_year, $request->contracted_month);
        }

        // If redirected from statitics index page & has status history requested (Table 2)
        if ($request->has_status_history) {
            $query = self::filterRecordsByStatusHistory(
                $query,
                $request->has_status_history_on_year,
                $request->has_status_history_on_month,
                $request->has_status_history_based_on,
                $request->has_status_history_based_on_value,
            );
        }

        // If redirected from plan.index page or manual planned status filter requested
        $query = self::filterRecordsByPlanStatus($query, $request->contracted_in_plan, $request->registered_in_plan);

        // If redirected from plan index page & stage 7 (НПР) requested
        if ($request->registered_on_requested_month_and_year) {
            $query = self::filterRecordsRegisteredOnRequestedMonthAndYear($query, $request->registered_year, $request->registered_month);
        }

        return $query;
    }

    /**
     * Filter records based on user roles and responsible countries.
     *
     * This method applies filters to the query based on the user's role. If the user is not an admin,
     * it limits the results to processes where the user is the assigned analyst or where the user's responsible
     * countries are associated with the manufacturer.
     *
     * Notes: Must use subquery for valid filtering!
     *
     * @param Illuminate\Http\Request $request The request object containing the user information.
     * @param Illuminate\Database\Eloquent\Builder $query The query builder instance to apply filters.
     * @return Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    private static function filterRecordsByRoles($request, $query)
    {
        // Get the current user
        $user = $request->user();

        // Get a list of country IDs the user is responsible for
        $responsibleCountryIDs = $user->responsibleCountries->pluck('id');

        // If the user is not an admin, apply additional filters
        if (Gate::denies('view-all-analysts-processes')) {
            $query->where(function ($subquery) use ($user, $responsibleCountryIDs) {
                $subquery->where(function ($innerQuery) use ($responsibleCountryIDs) {
                    $innerQuery->whereIn('country_code_id', $responsibleCountryIDs);
                })
                    ->orWhere(function ($innerQuery) use ($user) {
                        $innerQuery->whereHas('manufacturer', function ($manufacturersQuery) use ($user) {
                            // Filter for records where the user is the assigned analyst
                            $manufacturersQuery->where('analyst_user_id', $user->id);
                        });
                    });
            });
        }

        // Return the modified query
        return $query;
    }


    /**
     * Apply filters to the query based on the manufacturer's country.
     *
     * This function filters the processes based on the manufacturer’s country,
     * delegating the filtering logic to the Manufacturer model.
     *
     * @param Illuminate\Http\Request $request The HTTP request object containing filter parameters.
     * @param Illuminate\Database\Eloquent\Builder $query The query builder instance to apply filters to.
     * @return Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public static function filterSpecificManufacturerCountry($request, $query)
    {
        // Check if the request includes a specific manufacturer country filter
        if ($request->specific_manufacturer_country) {
            // Apply the manufacturer country filter using the Manufacturer model's filter function
            $query->whereHas('manufacturer', function ($manufacturersQuery) use ($request) {
                return Manufacturer::filterSpecificCountry($request, $manufacturersQuery);
            });
        }

        return $query;
    }

    /**
     * Filter processes which have contract history (stage 5 (Kk)) for the requested month and year.
     *
     * This function is used in both statistics index page & processes index page.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param int $year
     * @param int $month
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function filterRecordsContractedOnRequestedMonthAndYear($query, $year, $month)
    {
        return $query->whereHas('statusHistory', function ($historyQuery) use ($year, $month) {
            $historyQuery->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->whereHas('status.generalStatus', function ($statusesQuery) {
                    $statusesQuery->where('stage', 5);
                });
        });
    }

    /**
     * Filter processes which have registered history (stage 7 (НПР)) for the requested month and year.
     *
     * This function is used in both plan index page & processes index page.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param int $year
     * @param int $month
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function filterRecordsRegisteredOnRequestedMonthAndYear($query, $year, $month)
    {
        return $query->whereHas('statusHistory', function ($historyQuery) use ($year, $month) {
            $historyQuery->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->whereHas('status.generalStatus', function ($statusesQuery) {
                    $statusesQuery->where('stage', 7);
                });
        });
    }

    /**
     * Filter records based on status history criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance to apply filters to.
     * @param string $basedOn 'id' | 'name_for_analysts'
     */
    public static function filterRecordsByStatusHistory($query, $year, $month, $basedOn, $basedOnValue)
    {
        $query = $query->whereHas('statusHistory', function ($historyQuery) use ($year, $month, $basedOn, $basedOnValue) {
            $historyQuery->whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->whereHas('status.generalStatus', function ($statusesQuery) use ($basedOn, $basedOnValue) {
                    $statusesQuery->where($basedOn, $basedOnValue);
                });
        });

        return $query;
    }

    /**
     * Filter records based on the 'contracted_in_plan' and 'registered_in_plan' status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool|null $contractedInPlan
     * @param bool|null $registeredInPlan
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterRecordsByPlanStatus($query, $contractedInPlan = null, $registeredInPlan = null)
    {
        if (!is_null($contractedInPlan)) {
            $query->where('contracted_in_plan', $contractedInPlan);
        }

        if (!is_null($registeredInPlan)) {
            $query->where('registered_in_plan', $registeredInPlan);
        }

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
    | Create, Update and Duplicate
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

    public static function duplicateFromRequest($request)
    {
        $instance = self::create($request->all());

        // BelongsToMany relations
        $instance->clinicalTrialCountries()->attach($request->input('clinicalTrialCountries'));
        $instance->responsiblePeople()->attach($request->input('responsiblePeople'));

        // HasMany relations
        $instance->storeComment($request->comment);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Validate and set the status_update_date
     * on the creating event of the model instance.
     *
     * This function checks if the process is historical and sets the status_update_date
     * accordingly. If the process is historical, it uses the provided historical_date.
     * Otherwise, it sets the status_update_date to the current date and time.
     */
    private function validateStatusUpdateDateOnCreating()
    {
        $isHistorical = request()->input('is_historical');
        $historicalDate = request()->input('historical_date');

        $this->status_update_date = $isHistorical ? $historicalDate : now();
    }

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
     * Handle status update on the updating event of the model instance.
     *
     * If the status has changed, this method closes the current status history,
     * creates a new status history, and updates the status_update_date.
     */
    private function handleStatusUpdate()
    {
        // handle status history
        if ($this->isDirty('status_id')) {
            $this->currentStatusHistory->close();
            $this->status_update_date = now();
            $this->createNewStatusHistory();
        }
    }
    /**
     * Sends notifications to admins when the status of the model changes to stage 5 (Kk).
     * Notifications are triggered only if the 'status_id' attribute of the model is dirty (changed).
     *
     * @return void
     */
    private function notifyAdminsOnStatusUpdate()
    {
        if ($this->isDirty('status_id')) {
            $status = ProcessStatus::find($this->status_id);

            if ($status->generalStatus->stage == 5) {
                $notification = new ProcessOnContractStage($this, $status->name);

                User::all()->each(function ($user) use ($notification) {
                    if (Gate::forUser($user)->allows('recieve-notification-on-process-contract')) {
                        $user->notify($notification);
                    }
                });
            }
        }
    }

    /**
     * Validate and update the responsible_people_update_date attribute
     * on the saving event of the model instance.
     */
    private function validateResponsiblePeopleUpdateDate()
    {
        // set as now() on creating, because responsible people field is required from stage 1
        if (!$this->responsible_people_update_date) {
            $this->responsible_people_update_date = now();
        } else {
            // Compare the current responsible people IDs with the requested responsible people IDs
            $requestedIDs = request()->input('responsiblePeople', []);
            $instanceIDs = $this->responsiblePeople->pluck('id')->toArray();

            // Check for differences between the current and requested responsible people IDs
            if (count(array_diff($requestedIDs, $instanceIDs)) || count(array_diff($instanceIDs, $requestedIDs))) {
                // If there are any differences, update the responsible_people_update_date to the current date and time
                $this->responsible_people_update_date = now();
            }
        }
    }

    /**
     * Validate and update the manufacturer_followed_offered_price_in_usd attribute
     * on the created and updated events of the model instance.
     *
     * Important!
     * Timestamps are temporarily disabled because the price in USD is also updated daily via cron.
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
     * Add general statuses with periods for a collection of records.
     *
     * This method processes a collection of records and adds general status periods
     * based on the status history of each record. It clones the general statuses to
     * avoid modifying the original collection, calculates the start and end dates,
     * duration days, and duration days ratio for each general status.
     *
     * @param \Illuminate\Database\Eloquent\Collection $records The collection of records to process.
     *
     * @return void
     */
    public static function addGeneralStatusPeriodsForRecords($records)
    {
        // Load the statusHistory relationship for all records to avoid N+1 query problem
        $records->load('statusHistory');

        // Get all general statuses
        $generalStatuses = ProcessGeneralStatus::getAll();

        foreach ($records as $instance) {
            // Clone general statuses to avoid modifying the original collection
            $clonedGeneralStatuses = $generalStatuses->map(function ($item) {
                return clone $item;
            });

            foreach ($clonedGeneralStatuses as $generalStatus) {
                // Filter status histories related to the current general status
                $histories = $instance->statusHistory->filter(function ($history) use ($generalStatus) {
                    return $history->status->generalStatus->id === $generalStatus->id;
                });

                // Skip if no histories found
                if ($histories->isEmpty()) continue;

                // Sort histories by ID to find the first and last history
                $firstHistory = $histories->sortBy('id')->first();
                $lastHistory = $histories->sortByDesc('id')->first();

                // Set the start_date of the general status
                $generalStatus->start_date = $firstHistory->start_date;

                // Set the end_date of the general status
                $generalStatus->end_date = $lastHistory->end_date ?: Carbon::now();

                // Calculate duration_days for not closed status history
                // Process current status last history must not be closed logically
                $lastHistory->duration_days = $lastHistory->duration_days ?? round($lastHistory->start_date->diffInDays(now(), true));

                // Then calculate the total duration_days
                $generalStatus->duration_days = $histories->sum('duration_days');
            }

            // Calculate the highest duration_days among all general statuses
            $highestPeriod = $clonedGeneralStatuses->max('duration_days') ?: 1;

            // Calculate duration_days_ratio for each general status
            foreach ($clonedGeneralStatuses as $generalStatus) {
                $generalStatus->duration_days_ratio = $generalStatus->duration_days
                    ? round($generalStatus->duration_days * 100 / $highestPeriod)
                    : 0;
            }

            // Assign the cloned general statuses to the instance
            $instance->general_statuses_with_periods = $clonedGeneralStatuses;
        }
    }

    /**
     * Update all manufacturer_followed_offered_price_in_usd for instances via a cron job daily.
     */
    public static function updateAllManufacturerPricesInUSD()
    {
        self::weach(function ($instance) {
            $instance->validateManufacturerPriceInUSD();
        });
    }

    /**
     * Provides the default table columns along with their properties
     * based on user roles
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultTableColumnsForUser($user)
    {
        if (Gate::forUser($user)->denies('view-vps')) {
            return null;
        }

        $order = 1;
        $columns = array();

        if (Gate::forUser($user)->allows('edit-vps')) {
            array_push(
                $columns,
                ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'ID', 'order' => $order++, 'width' => 60, 'visible' => 1],
        );

        if (Gate::forUser($user)->allows('edit-vps')) {
            array_push(
                $columns,
                ['name' => 'Duplicate', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'Status date', 'order' => $order++, 'width' => 116, 'visible' => 1],
        );

        if (Gate::forUser($user)->allows('control-spg-processes')) {
            array_push(
                $columns,
                ['name' => '5Кк', 'order' => $order++, 'width' => 40, 'visible' => 1],
                ['name' => '7НПР', 'order' => $order++, 'width' => 50, 'visible' => 1],
            );
        }

        if (Gate::forUser($user)->allows('mark-process-as-ready-for-order')) {
            array_push(
                $columns,
                ['name' => '9Зя', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'Product status', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'Product status An*', 'order' => $order++, 'width' => 136, 'visible' => 1],
            ['name' => 'General status', 'order' => $order++, 'width' => 110, 'visible' => 1],

            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Search country', 'order' => $order++, 'width' => 130, 'visible' => 1],

            ['name' => 'Manufacturer category', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Manufacturer country', 'order' => $order++, 'width' => 174, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],

            ['name' => 'Generic', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Dosage', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],

            ['name' => 'Shelf life', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'MOQ', 'order' => $order++, 'width' => 140, 'visible' => 1],

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

            ['name' => 'Product class', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Brand Eng', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Brand Rus', 'order' => $order++, 'width' => 100, 'visible' => 1],

            ['name' => 'Date of forecast', 'order' => $order++, 'width' => 136, 'visible' => 1],
            ['name' => 'Forecast 1 year', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Forecast 2 year', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Forecast 3 year', 'order' => $order++, 'width' => 130, 'visible' => 1],

            ['name' => 'Dossier status', 'order' => $order++, 'width' => 124, 'visible' => 1],
            ['name' => 'Year Cr/Be', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Countries Cr/Be', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Country ich', 'order' => $order++, 'width' => 108, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Down payment 1', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Down payment 2', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Down payment condition', 'order' => $order++, 'width' => 194, 'visible' => 1],

            ['name' => 'Responsible', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Responsible update date', 'order' => $order++, 'width' => 250, 'visible' => 1],
            ['name' => 'Days have passed', 'order' => $order++, 'width' => 130, 'visible' => 1],

            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],

            ['name' => 'Comments', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
        );

        if (Gate::forUser($user)->allows('edit-processes-status-history')) {
            array_push(
                $columns,
                ['name' => 'History', 'order' => $order++, 'width' => 72, 'visible' => 1]
            );
        }

        array_push(
            $columns,
            ['name' => 'ВП', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'ПО', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'АЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'СЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Кк', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'КД', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'НПР', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Р', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Зя', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Отмена', 'order' => $order++, 'width' => 200, 'visible' => 1],
        );

        return $columns;
    }

    /**
     * Provides the default table columns along with their properties.
     *
     * These columns are typically used to display data in tables,
     * such as on index and trash pages, and are iterated over in a loop.
     *
     * @return array
     */
    public static function getDefaultForOrderTableColumnsForUser($user)
    {
        if (Gate::forUser($user)->denies('view-processes-for-order')) {
            return null;
        }

        $order = 1;
        $columns = array();

        if (Gate::forUser($user)->allows('edit-processes-for-order')) {
            array_push(
                $columns,
                ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            );
        }

        array_push(
            $columns,
            ['name' => 'Brand name ENG', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Brand name RUS', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Orders', 'order' => $order++, 'width' => 86, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'VPS Brand Eng', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'VPS Brand Rus', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 130, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 144, 'visible' => 1],
        );

        return $columns;
    }

    /**
     * Prepare chunked records for export
     */
    public static function prepareRecordsForExport($records)
    {
        self::addGeneralStatusPeriodsForRecords($records);

        return $records;
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
            $this->status_update_date,
            $this->searchCountry->name,
            $this->status->name,
            $this->status->generalStatus->name_for_analysts,
            $this->status->generalStatus->name,
            $this->manufacturer->category->name,
            $this->manufacturer->name,
            $this->manufacturer->country->name,
            $this->manufacturer->bdm->name,
            $this->manufacturer->analyst->name,
            $this->product->inn->name,
            $this->product->form->name,
            $this->product->dosage,
            $this->product->pack,
            $this->marketingAuthorizationHolder?->name,
            $this->comments->pluck('body')->implode(' / '),
            $this->lastComment?->created_at,
            $this->manufacturer_first_offered_price,
            $this->manufacturer_followed_offered_price,
            $this->currency?->name,
            $this->manufacturer_followed_offered_price_in_usd,
            $this->agreed_price,
            $this->our_followed_offered_price,
            $this->our_first_offered_price,
            $this->increased_price,
            $this->increased_price_percentage,
            $this->increased_price_date,
            $this->product->shelfLife->name,
            $this->product->moq,
            $this->dossier_status,
            $this->clinical_trial_year,
            $this->clinicalTrialCountries->pluck('name')->implode(' '),
            $this->clinical_trial_ich_country,
            $this->product->zones->pluck('name')->implode(' '),
            $this->down_payment_1,
            $this->down_payment_2,
            $this->down_payment_condition,
            $this->forecast_year_1_update_date,
            $this->forecast_year_1,
            $this->forecast_year_2,
            $this->forecast_year_3,
            $this->responsiblePeople->pluck('name')->implode(' '),
            $this->responsible_people_update_date,
            $this->days_past,
            $this->trademark_en,
            $this->trademark_ru,
            $this->created_at,
            $this->updated_at,
            $this->product->class->name,
            $this->general_statuses_with_periods[0]->start_date,
            $this->general_statuses_with_periods[1]->start_date,
            $this->general_statuses_with_periods[2]->start_date,
            $this->general_statuses_with_periods[3]->start_date,
            $this->general_statuses_with_periods[4]->start_date,
            $this->general_statuses_with_periods[5]->start_date,
            $this->general_statuses_with_periods[6]->start_date,
            $this->general_statuses_with_periods[7]->start_date,
            $this->general_statuses_with_periods[8]->start_date,
            $this->general_statuses_with_periods[9]->start_date,
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
            'start_date' => $this->status_update_date,
        ]);
    }

    // Implement the abstract method declared in the CommentableModel class
    public function getTitle(): string
    {
        return trans('Process') . ' #' . $this->id . ' / ' . $this->searchCountry->name;
    }

    public function isReadyForPlan()
    {
        return $this->status->generalStatus->stage >= 5;
    }

    public function canBeMarkedAsReadyForOrder()
    {
        return $this->status->generalStatus->stage >= 9;
    }

    public function markAsReadyForOrder()
    {
        if (!$this->canBeMarkedAsReadyForOrder()) {
            return false;
        }

        if (!$this->is_ready_for_order) {
            $this->is_ready_for_order = true;
            $this->readiness_for_order_date = $this->readiness_for_order_date ?: now();
            $this->fixed_trademark_en_for_order = 'Brand (ENG)';
            $this->fixed_trademark_ru_for_order = 'Brand (RU)';
            $this->timestamps = false;
            $this->saveQuietly();

            $notification = new ProcessMarkedAsReadyForOrder($this);

            User::all()->each(function ($user) use ($notification) {
                if (Gate::forUser($user)->allows('view-processes-for-order')) {
                    $user->notify($notification);
                }
            });
        }

        return true;
    }

    public static function pluckAllEnTrademarks()
    {
        return self::distinct()->pluck('trademark_en');
    }

    public static function pluckAllRuTrademarks() {
        return self::distinct()->pluck('trademark_ru');
    }

    public static function pluckAllFixedEnTrademarks()
    {
        return self::distinct()->pluck('fixed_trademark_en_for_order');
    }

    public static function pluckAllFixedRuTrademarks() {
        return self::distinct()->pluck('fixed_trademark_ru_for_order');
    }
}
