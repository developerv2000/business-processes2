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

    const ADMINISTRATOR_NAME = 'Administrator';     // Full access. Only admins can force delete records from trash!
    const MODERATOR_NAME = 'Moderator';             // Full access except Admin part. Can view/create/edit/update/delete and export.
    const ANALYST_NAME = 'Analyst';                 // Same as moderator + displays as Analysts in all tables (can be selected as Alyst).
    const BDM_NAME = 'Bdm';                         // Same as moderator + displays as BDM in all tables (can be selected as BDM).
    const INACTIVE_NAME = 'Inactive';               // No access, can not login
    const GUEST_NAME = 'Guest';                     // Can only view all pages except Admin part. Can not create/edit/update/delete and export.

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
