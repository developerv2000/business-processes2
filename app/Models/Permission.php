<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    const CAN_EDIT_USERS_NAME = 'can-edit-users';
    const CAN_EDIT_SECONDARY_TABLES_NAME = 'can-edit-secondary-tables';

    const CAN_NOT_DOWNLOAD_NAME = 'can-not-download';

    const CAN_NOT_EDIT_NAME = 'can-not-edit';
    const CAN_NOT_EDIT_EPP_NAME = 'can-not-edit-EPP';
    const CAN_NOT_EDIT_KVPP_NAME = 'can-not-edit-KVPP';
    const CAN_NOT_EDIT_IVP_NAME = 'can-not-edit-IVP';
    const CAN_NOT_EDIT_VPS_NAME = 'can-not-edit-VPS';
    const CAN_NOT_EDIT_MEETINGS_NAME = 'can-not-edit-Meetings';

    const CAN_NOT_VIEW_EPP_NAME = 'can-not-view-EPP';
    const CAN_NOT_VIEW_KVPP_NAME = 'can-not-view-KVPP';
    const CAN_NOT_VIEW_IVP_NAME = 'can-not-view-IVP';
    const CAN_NOT_VIEW_VPS_NAME = 'can-not-view-VPS';
    const CAN_NOT_VIEW_MEETINGS_NAME = 'can-not-view-Meetings';

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
