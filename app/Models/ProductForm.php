<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductForm extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $with = [
        'parent'
    ];

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }
}
