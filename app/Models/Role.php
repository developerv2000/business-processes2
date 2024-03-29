<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const ADMIN_NAME = 'Admin';
    const MODERATOR_NAME = 'Moderator';
    const ANALYST_NAME = 'Analyst';
    const BDM_NAME = 'Bdm';
    const ROBOT_NAME = 'Robot';
    const TRAINEE_NAME = 'Trainee';

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }
}
