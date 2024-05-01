<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAuthorizationHolder extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public static function getAll()
    {
        return self::orderBy('id')->get();
    }
}
