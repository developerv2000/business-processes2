<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inn extends Model implements TemplatedModelInterface
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class);
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }

    /**
     * Get all records ordered by usage count
     *
     * Used on filtering and creating/editing of related models
     */
    public static function getAllPrioritized()
    {
        return self::withCount(['products', 'kvpps'])
            ->get()
            ->sortByDesc(function ($instance) {
                return $instance->products_count + $instance->kvpps_count;
            });
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->products()->count() + $this->kvpps()->count();
    }

    /**
     * Retrieve all records that have been used by Kvpp.
     *
     * Used in Kvpp filtering
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOnlyKvppInns()
    {
        return self::has('kvpps')
            ->withCount(['products', 'kvpps'])
            ->get()
            ->sortByDesc(function ($instance) {
                return $instance->products_count + $instance->kvpps_count;
            });
    }
}
