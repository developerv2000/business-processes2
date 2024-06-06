<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model implements TemplatedModelInterface
{
    use HasFactory;

    const DEFAULT_SELECTED_IDS = [2]; // Zone ||

    public $timestamps = false;
    protected $guarded = ['id'];

    public function manufacturers()
    {
        return $this->belongsToMany(Manufacturer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public static function getAll()
    {
        return self::orderBy('id')->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->manufacturers()->count()
            + $this->products()->count();
    }

    /**
     * Used to select default values on products create form
     */
    public function getSelectedByDefaultAttribute()
    {
        return $this->id == in_array($this->id, self::DEFAULT_SELECTED_IDS) ?? false;
    }
}
