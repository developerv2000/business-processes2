<?php

namespace App\Models;

use App\Http\Requests\KvppStoreRequest;
use App\Support\Helper;
use App\Support\Traits\Commentable;
use App\Support\Traits\ExportsRecords;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kvpp extends Model
{
    use HasFactory;
    use SoftDeletes;
    use MergesParamsToRequest;
    use Commentable;
    use ExportsRecords;

    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const EXCEL_TEMPLATE_STORAGE_PATH = 'app/excel/templates/kvpp.xlsx';
    const EXCEL_EXPORT_STORAGE_PATH = 'app/excel/exports/kvpp';

    protected $guarded = ['id'];

    protected $with = [
        'status',
        'country',
        'priority',
        'source',
        'inn',
        'form',
        'marketingAuthorizationHolder',
        'portfolioManager',
        'analyst',
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
            'date_of_forecast' => 'date:m-Y-d',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function status()
    {
        return $this->belongsTo(KvppStatus::class, 'status_id');
    }

    public function country()
    {
        return $this->belongsTo(CountryCode::class, 'country_code_id');
    }

    public function priority()
    {
        return $this->belongsTo(KvppPriority::class, 'priority_id');
    }

    public function source()
    {
        return $this->belongsTo(KvppSource::class, 'source_id');
    }

    public function inn()
    {
        return $this->belongsTo(Inn::class);
    }

    public function form()
    {
        return $this->belongsTo(ProductForm::class, 'form_id');
    }

    public function marketingAuthorizationHolder()
    {
        return $this->belongsTo(MarketingAuthorizationHolder::class);
    }

    public function portfolioManager()
    {
        return $this->belongsTo(PortfolioManager::class);
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    public function getCoincidentProcessesAttribute()
    {
        return Process::whereHas('product', function ($query) {
            $query->where([
                'inn_id' => $this->inn_id,
                'form_id' => $this->form_id,
                'dosage' => $this->dosage,
                'pack' => $this->pack,
            ]);
        })
            ->where('country_code_id', $this->country_code_id)
            ->select('id', 'status_id')
            ->withOnly('status')
            ->get();
    }

    public function getCoincidentProductsCountAttribute()
    {
        return Product::where([
            'inn_id' => $this->inn_id,
            'form_id' => $this->form_id,
        ])->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */
    
    protected static function booted(): void
    {
        static::forceDeleting(function ($instance) {
            foreach ($instance->comments as $comment) {
                $comment->delete();
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
            'country_code_id',
            'priority_id',
            'source_id',
            'inn_id',
            'form_id',
            'marketing_authorization_holder_id',
            'portfolio_manager_id',
            'analyst_user_id',
            'id',
        ];

        $whereLikeAttributes = [
            'dosage',
            'pack',
        ];

        $dateRangeAttributes = [
            'created_at',
            'updated_at',
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);
        $query = Helper::filterQueryLikeStatements($request, $query, $whereLikeAttributes);
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

    /**
     * Retrieve all used inns.
     *
     * Used in filtering
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllUsedInns()
    {
        return Inn::has('kvpps')->orderBy('name')->get();
    }

    /**
     * Retrieve all used forms.
     *
     * Used in filtering
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllUsedForms()
    {
        return ProductForm::has('kvpps')->orderBy('name')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Create and Update
    |--------------------------------------------------------------------------
    */

    /**
     * Create instances from the given request.
     *
     * This method iterates over each marketing_authorization_holder_ids,
     * validates request for each of marketing_authorization_holder_id,
     * and creates new instances on validation success.
     *
     * @param \Illuminate\Http\Request $request The request containing data.
     * @return void
     */
    public static function createFromRequest($request)
    {
        // Extract marketing authorization holder IDs from the request
        $mahIDs = $request->input('marketing_authorization_holder_ids');

        // Iterate over each marketing authorization holder ID
        foreach ($mahIDs as $id) {
            // Merge the marketing authorization holder ID into the request
            $mergedRequest = $request->merge(['marketing_authorization_holder_id' => $id]);

            // Create a KvppStoreRequest instance from the merged request
            $formRequest = KvppStoreRequest::createFrom($mergedRequest);

            // Create a validator instance
            $validator = app('validator')->make(
                $formRequest->all(),
                $formRequest->rules(),
                $formRequest->messages()
            );

            // Perform validation
            $validator->validate();

            // Create an instance using the merged request data
            $instance = self::create($mergedRequest->all());

            // Store HasMany relations
            $instance->storeComment($request->comment);
        }
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());

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
            $this->status->name,
            $this->country->name,
            $this->priority->name,
            $this->coincident_processes->count(),
            $this->coincident_products_count,
            $this->source->name,
            $this->inn->name,
            $this->form->name,
            $this->form->parent_name,
            $this->dosage,
            $this->pack,
            $this->marketingAuthorizationHolder->name,
            $this->information,
            $this->comments->pluck('body')->implode(' / '),
            $this->lastComment?->created_at,
            $this->date_of_forecast,
            $this->forecast_year_1,
            $this->forecast_year_2,
            $this->forecast_year_3,
            $this->portfolioManager?->name,
            $this->analyst?->name,
            $this->created_at,
            $this->updated_at,
        ];
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
        $similarRecords = Kvpp::where([
            'inn_id' => $request->inn_id,
            'dosage' => $request->dosage,
            'pack' => $request->pack,
            'country_code_id' => $request->country_code_id,
        ])
            ->whereIn('form_id', $formFamilyIDs)
            ->get();

        return $similarRecords;
    }
}
