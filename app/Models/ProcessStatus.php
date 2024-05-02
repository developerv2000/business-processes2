<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $with = [
        'generalStatus'
    ];

    public function generalStatus()
    {
        return $this->belongsTo(ProcessGeneralStatus::class, 'general_status_id');
    }

    public function processes()
    {
        return $this->hasMany(Process::class);
    }
}
