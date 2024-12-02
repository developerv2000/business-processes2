<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentType extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    const PREPAYMENT_NAME = 'Prepayment';
    const FINAL_PAYMENT_NAME = 'Final payment';
    const FULL_PAYMENT_NAME = 'Full payment';

    const PREPAYMENT_ID = 1;
    const FINAL_PAYMENT_ID = 2;
    const FULL_PAYMENT_ID = 3;

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
