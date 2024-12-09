<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItemCategory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    const PRODUCT_NAME = 'Product';
    const OTHER_PAYMENTS_NAME = 'Other payments';
    const SERVICE_NAME = 'Service';

    const PRODUCT_ID = 1;
    const OTHER_PAYMENTS_ID = 2;
    const SERVICE_ID = 3;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
