<?php

namespace App\Models;

use App\Support\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use Commentable;

    protected $guarded = ['id'];

    protected $with = [
        'inn',
        'form',
        'shelfLife',
        'class',
        'zones',
        'lastComment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class)->withTrashed();
    }

    public function inn()
    {
        return $this->belongsTo(Inn::class);
    }

    public function form()
    {
        return $this->belongsTo(ProductForm::class, 'form_id');
    }

    public function shelfLife()
    {
        return $this->belongsTo(ProductShelfLife::class);
    }

    public function class()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }
}
