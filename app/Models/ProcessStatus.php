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
        // Check if the user is an admin
        $isAdministrator = request()->user()->isAdministrator();

        // Query records, applying additional filters if the user is not an admin
        $records = self::when(!$isAdministrator, function ($query) {
            $query->whereHas('generalStatus', function ($subquery) {
                $subquery->where('visible_only_for_admins', false);
            });
        })->orderBy('id', 'asc')->get();

        return $records;
    }
}
