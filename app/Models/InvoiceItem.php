<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    use MergesParamsToRequest;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    protected $guarded = ['id'];

    protected $with = [
        'category',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function category()
    {
        return $this->belongsTo(InvoiceItemCategory::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class)->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    public function getPrepaymentInvoiceItemAttribute()
    {
        if (!$this->invoice->isFinalPayment() || !$this->isProductCategory()) {
            return null;
        }

        // Use eager-loaded relationships when possible
        return self::where('order_product_id', $this->order_product_id)
            ->whereHas('invoice', function ($invoiceQuery) {
                $invoiceQuery->where('payment_type_id', InvoicePaymentType::PREPAYMENT_ID);
            })
            ->first();
    }

    public function getTotalPriceAttribute()
    {
        $totalPrice = $this->quantity * $this->price;

        return round($totalPrice, 2);
    }

    public function getPrepaymentAmountAttribute()
    {
        if (!$this->invoice->isFinalPayment() || !$this->isProductCategory()) {
            return 0;
        }

        return $this->prepayment_invoice_item->amount_paid;
    }

    public function getPaymentDueAttribute()
    {
        switch ($this->invoice->paymentType->name) {
            case InvoicePaymentType::PREPAYMENT_NAME:
                return round(Helper::calculatePercentage($this->total_price, $this->invoice->prepayment_percentage), 2);
            case InvoicePaymentType::FINAL_PAYMENT_NAME:
                return $this->total_price - $this->prepayment_amount;
            case InvoicePaymentType::FULL_PAYMENT_NAME:
                return $this->total_price;
        }
    }

    public function getTermsAttribute()
    {
        switch ($this->invoice->paymentType->name) {
            case InvoicePaymentType::PREPAYMENT_NAME:
                return $this->invoice->prepayment_percentage;
            case InvoicePaymentType::FINAL_PAYMENT_NAME:
                return round(Helper::calculatePercentageOfTotal($this->total_price, $this->payment_due), 2);
            case InvoicePaymentType::FULL_PAYMENT_NAME:
                return 100;
        }
    }

    public function getPaymentDifferenceAttribute()
    {
        return $this->amount_paid - $this->payment_due;
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void {}

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
        // return $query->with([
        //     'manufacturer' => function ($query) {
        //         $query->select('id', 'name', 'country_id', 'bdm_user_id', 'analyst_user_id')
        //             ->withOnly(['country', 'bdm:id,name,photo', 'analyst:id,name,photo']);
        //     },
        // ]);
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
            'invoice_id',
        ];

        $query = Helper::filterQueryWhereEqualStatements($request, $query, $whereEqualAttributes);

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
        // $records = $records->withComplexRelations();

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
        self::create($request->all());
    }

    public function updateFromRequest($request)
    {
        $this->update($request->all());
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    public function isProductCategory()
    {
        return $this->category_id == InvoiceItemCategory::PRODUCT_ID;
    }

    public function isOtherPaymentsCategory()
    {
        return $this->category_id == InvoiceItemCategory::OTHER_PAYMENTS_ID;
    }

    public function isServiceCategory()
    {
        return $this->category_id == InvoiceItemCategory::SERVICE_ID;
    }
}
