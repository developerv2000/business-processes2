<?php

namespace App\Models;

use App\Support\Helper;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    use MergesParamsToRequest;

    const DEFAULT_ORDER_BY = 'updated_at';
    const DEFAULT_ORDER_TYPE = 'desc';
    const DEFAULT_PAGINATION_LIMIT = 50;

    const FILE_PATH = 'files/invoices';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'datetime',
        'sent_for_payment_date' => 'datetime',
        'payment_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function category()
    {
        return $this->belongsTo(InvoiceCategory::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(InvoicePaymentType::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payer()
    {
        return $this->belongsTo(Payer::class);
    }

    // only for invoices of service category
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'service_category_manufacturer_id');
    }

    // only for invoices of service category
    public function country()
    {
        return $this->belongsTo(CountryCode::class, 'service_category_country_code_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */

    public function getFileAssetUrlAttribute()
    {
        return asset(self::FILE_PATH . '/' . $this->file);
    }

    public function getTotalPriceAttribute()
    {
        $totalPrice = $this->items->sum('total_price');

        // Use bcround for better precision handling if financial values are involved
        return round($totalPrice, 2);
    }

    public function getPrepaymentAmountAttribute()
    {
        if (!$this->isFinalPayment()) {
            return 0;
        }

        $prepaymentAmount = $this->items->sum('prepayment_amount');

        return round($prepaymentAmount, 2);
    }

    public function getPaymentDueAttribute()
    {
        $paymentDue = $this->items->sum('payment_due');

        return round($paymentDue, 2);
    }

    public function getTermsAttribute()
    {
        switch ($this->paymentType->name) {
            case InvoicePaymentType::PREPAYMENT_NAME:
                return $this->prepayment_percentage;
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

    public function getAmountPaidAttribute()
    {
        $paymentDue = $this->items->sum('amount_paid');

        return round($paymentDue, 2);
    }

    public function getStatusAttribute()
    {
        if ($this->cancelled) {
            return 'Cancelled';
        }

        return $this->payment_date ? 'Paid' : 'Unpaid';
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::updating(function ($instance) {
            // Check if 'payment_date' original value was null but the current value is not null
            $wasNull = is_null($instance->getOriginal('payment_date'));
            $isNotNull = !is_null($instance->payment_date);

            if ($wasNull && $isNotNull) {
                foreach ($instance->items as $item) {
                    $item->amount_paid = $item->payment_due;
                    $item->saveQuietly();
                }
            }
        });

        static::deleting(function ($instance) {
            $instance->orders()->detach();

            foreach ($instance->items as $item) {
                $item->delete();
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
        $whereInAttributes = [
            'id',
        ];

        $belongsToManyRelations = [
            'orders',
        ];

        $query = Helper::filterQueryWhereInStatements($request, $query, $whereInAttributes);
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
            ->orderBy('id', $request->orderType)
            ->withCount('items');

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

    public static function createGoodsFromRequest($request)
    {
        // Merge Goods category into request object
        $request->merge([
            'category_id' => InvoiceCategory::GOODS_ID,
        ]);

        // Remove prepayment_percentage from request object
        if ($request->input('payment_type_id') != InvoicePaymentType::PREPAYMENT_ID) {
            $request->merge([
                'prepayment_percentage' => null,
            ]);
        }

        // Create invoice
        $invoice = Invoice::create($request->only([
            'name',
            'date',
            'category_id',
            'payment_type_id',
            'currency_id',
            'prepayment_percentage',
            'payer_id',
            'group_name',
        ]));

        // Attach invoice orders
        $invoice->orders()->attach($request->order_ids);

        // Create invoice products
        foreach ($request->input('products', []) as $product) {
            // Skip unselected products
            if (!key_exists('id', $product)) {
                continue;
            }

            // Create product
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'category_id' => InvoiceItemCategory::PRODUCT_ID,
                'order_product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
            ]);
        }

        // Create invoice other payments
        foreach ($request->input('other_payments', []) as $payment) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'category_id' => InvoiceItemCategory::OTHER_PAYMENTS_ID,
                'description' => $payment['description'],
                'quantity' => $payment['quantity'],
                'price' => $payment['price'],
            ]);
        }

        // Upload file
        Helper::uploadModelFile($invoice, 'file', $invoice->name, public_path(self::FILE_PATH));
    }

    public static function createServicesFromRequest($request)
    {
        // Create invoice
        $invoice = Invoice::create($request->only([
            'name',
            'date',
            'category_id',
            'payment_type_id',
            'currency_id',
            'payer_id',
            'group_name',
            'service_category_manufacturer_id',
            'service_category_country_code_id',
        ]));

        // Create invoice services
        foreach ($request->input('services', []) as $service) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'category_id' => InvoiceItemCategory::SERVICE_ID,
                'description' => $service['description'],
                'quantity' => $service['quantity'],
                'price' => $service['price'],
            ]);
        }

        // Upload file
        Helper::uploadModelFile($invoice, 'file', $invoice->name, public_path(self::FILE_PATH));
    }

    public function updateFromRequest($request)
    {
        $this->update($request->except('file'));

        // Upload file
        Helper::uploadModelFile($this, 'file', $this->name, public_path(self::FILE_PATH));
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    public function isGoodsCategory()
    {
        return $this->category_id == InvoiceCategory::GOODS_ID;
    }

    public function isServicesCategory()
    {
        return $this->category_id == InvoiceCategory::SERVICE_ID;
    }

    public function isPrepayment()
    {
        return $this->paymentType->name == InvoicePaymentType::PREPAYMENT_NAME;
    }

    public function isFinalPayment()
    {
        return $this->paymentType->name == InvoicePaymentType::FINAL_PAYMENT_NAME;
    }

    public function isFullPayment()
    {
        return $this->paymentType->name == InvoicePaymentType::FULL_PAYMENT_NAME;
    }
}
