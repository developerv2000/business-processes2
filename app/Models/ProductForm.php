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

    /**
     *
     */
    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : $this->name;
    }

    public static function getAllMinified()
    {
        return self::orderBy('name')->withOnly([])->get();
    }
}
