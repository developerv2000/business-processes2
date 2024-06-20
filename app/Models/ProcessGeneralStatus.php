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

    public static function getAll()
    {
        return self::orderBy('id', 'asc')->get();
    }

    /**
     * Pluck all unique name_for_analysts
     *
     * Used on process filtering
     */
    public static function getUniqueStatusNamesForAnalysts()
    {
        return self::orderBy('id', 'asc')->pluck('name_for_analysts')->unique();
    }
}
