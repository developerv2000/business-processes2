<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturerPresence extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
