<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Update notes:
    // 1. Admin renamed with Administrator
    // 2. Robot renamed with Inactive
    // 3. Trainee (old id = 6) renamed with Guest

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const ADMINISTRATOR_NAME = 'Administrator';     // Full access. Only admins can force delete records from trash!
    const MODERATOR_NAME = 'Moderator';             // Full access except Admin part. Can view/create/edit/update/delete and export.
    const ANALYST_NAME = 'Analyst';                 // User can be selected as analyst in all tables, filters etc.
    const BDM_NAME = 'Bdm';                         // User can be selected as BDM in all tables, filters etc.
    const INACTIVE_NAME = 'Inactive';               // No access, can not login
    const GUEST_NAME = 'Guest';                     // Can only view all pages except Admin part. Can not create/edit/update/delete and export.

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public $timestamps = false;

    protected $with = [
        'permissions',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
    */

    public static function getAll()
    {
        return self::orderBy('name')->get();
    }
}
