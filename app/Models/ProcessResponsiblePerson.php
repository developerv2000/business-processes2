<?php

namespace App\Models;

use App\Support\Contracts\TemplatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessResponsiblePerson extends Model implements TemplatedModelInterface
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_process_responsible_people', 'responsible_person_id', 'process_id');
    }

    // Implement the method declared in the TemplatedModelInterface
    public function getUsageCountAttribute(): int
    {
        return $this->processes()->count();
    }

    public static function getAll()
    {
        return self::orderBy('name', 'asc')->get();
    }
}
