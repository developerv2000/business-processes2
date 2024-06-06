<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model implements TemplatedModelInterface
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    // Eager load relations count for usage_count attribute perfomance
    public $withCount = [
        'manufacturers',
    ];

    public function manufacturers()
    {
        return $this->hasMany(Manufacturer::class);
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->manufacturers_count;
    }
}
