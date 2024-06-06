<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model implements TemplatedModelInterface
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    // Eager load relations count for usage_count attribute perfomance
    public $withCount = [
        'processes',
        'kvpps',
    ];

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class);
    }

    public static function getAllPrioritized()
    {
        return self::withCount(['processes', 'kvpps'])
            ->orderByRaw('processes_count + kvpps_count DESC')
            ->get();
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->processes_count + $this->kvpps_count;
    }
}
