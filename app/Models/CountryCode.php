<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    public $timestamps = false;

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
}
