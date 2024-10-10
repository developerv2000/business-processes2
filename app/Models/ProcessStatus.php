<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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

    public static function getAll()
    {
        return self::orderBy('id', 'asc')->get();
    }

    /**
     * Get all records filtered based on user roles.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getAllFilteredByRoles()
    {
        $records = self::query();

        // Query records, applying additional filters if the user is not an admin
        if (Gate::denies('upgrade-process-status-after-contract')) {
            $records = $records->whereHas('generalStatus', function ($subquery) {
                $subquery->where('visible_only_for_admins', false);
            });
        }

        $records = $records->orderBy('id', 'asc')->get();

        return $records;
    }
}
