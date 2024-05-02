<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessGeneralStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function childs()
    {
        return $this->hasMany(ProcessStatus::class, 'general_status_id');
    }
}
