<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function manufacturers()
    {
        return $this->hasMany(Manufacturer::class);
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }
}
