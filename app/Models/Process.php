<?php

namespace App\Models;

use App\Support\Traits\Commentable;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use Commentable;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'created_at';
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
        return $this->belongsTo(CountryCode::class);
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
    | Events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::created(function ($instance) {
            $instance->createNewStatusHistory();
        });

        static::updating(function ($instance) {
            // Close the current status history and create a new one, if the status has changed
            if ($instance->isDirty('status_id')) {
                $instance->currentStatusHistory->close();
                $instance->createNewStatusHistory();
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
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! NOT DONE YET!!!!
     */
    public function scopeWithComplexRelations($query)
    {
        return $query;
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

    public static function createFromRequest($request)
    {
        $instance = self::create($request->all());

        // BelongsToMany relations
        $instance->clinicalTrialCountries()->attach($request->input('clinicalTrialCountries'));
        $instance->responsiblePeople()->attach($request->input('responsiblePeople'));

        // HasMany relations
        $instance->storeComment($request->comment);
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
}
