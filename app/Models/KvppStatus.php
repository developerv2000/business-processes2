<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KvppStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function kvpps()
    {
        return $this->hasMany(Kvpp::class, 'status_id');
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }
}
