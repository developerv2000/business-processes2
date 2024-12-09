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

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy('id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    public function isPrepayment()
    {
        return $this->name == self::PREPAYMENT_NAME;
    }

    public function isFinalPayment()
    {
        return $this->name == self::FINAL_PAYMENT_NAME;
    }

    public function isFullPayment()
    {
        return $this->name == self::FULL_PAYMENT_NAME;
    }
}
