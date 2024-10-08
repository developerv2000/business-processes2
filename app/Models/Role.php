<?php

namespace App\Models;

use App\Support\Traits\FindsRecordByName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use FindsRecordByName;

    // Update notes:
    // 1. Admin renamed with Administrator
    // 2. Robot renamed with Inactive
    // 3. Trainee (old id = 6) renamed with Guest

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    // Notes: Checkout RoleSeeder for full guide.

    const ADMINISTRATOR_NAME = 'Administrator';   // Full access. Doesn`t attach any role related permissions.
    const MODERATOR_NAME = 'Moderator';           // Can view/create/edit/update/delete and export 'Main part' and comments. Attachs role based permissions.
    const ANALYST_NAME = 'Analyst';               // User is assosiated as 'Analyst'. Doesn`t attach any role based permissions.
    const BDM_NAME = 'BDM';                       // User is assosiated as 'BDM'. Doesn`t attach any role related permissions.
    const INACTIVE_NAME = 'Inactive';             // No access, can`t login. Doesn`t attach any role related permissions.
    const GUEST_NAME = 'Guest';                   // Can only view 'Main part'. Can`t create/edit/update/delete and export. Attaches role based permissions.

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

    public static function getByName($name)
    {
        return self::where('name', $name)->firstOrFail();
    }
}
