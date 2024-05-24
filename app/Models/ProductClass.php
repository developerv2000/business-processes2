<?php

namespace App\Models;

use App\Support\Interfaces\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductClass extends Model implements TemplatedModelInterface
{
    use HasFactory;

    const DEFAULT_SELECTED_ID = 1; // ЛС

    public $timestamps = false;
    protected $guarded = ['id'];

    public function manufacturers()
    {
        return $this->belongsToMany(Manufacturer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'class_id');
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->manufacturers()->count()
            + $this->products()->count();
    }

    /**
     * Used to select default value on products create form
     */
    public function getSelectedByDefaultAttribute()
    {
        return $this->id == self::DEFAULT_SELECTED_ID ?? false;
    }
}
