<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCategory extends Model
{
    use HasFactory;

    const GOODS_ID = 1;
    const SERVICE_ID = 2;

    public $timestamps = false;
    protected $guarded = ['id'];

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
