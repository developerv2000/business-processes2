<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessResponsiblePerson extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_process_responsible_people', 'responsible_person_id', 'process_id');
    }
}
