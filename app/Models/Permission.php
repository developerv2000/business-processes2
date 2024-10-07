<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | View tables permissions
    |--------------------------------------------------------------------------
    */

    // Main
    const CAN_VIEW_EPP_NAME = 'can-view-EPP';
    const CAN_VIEW_KVPP_NAME = 'can-view-KVPP';
    const CAN_VIEW_IVP_NAME = 'can-view-IVP';
    const CAN_VIEW_VPS_NAME = 'can-view-VPS';
    const CAN_VIEW_MEETINGS_NAME = 'can-view-Meetings';
    const CAN_VIEW_KPE_NAME = 'can-view-KPE';
    const CAN_VIEW_SPG_NAME = 'can-view-SPG';

    const CAN_NOT_VIEW_EPP_NAME = 'can-not-view-EPP';
    const CAN_NOT_VIEW_KVPP_NAME = 'can-not-view-KVPP';
    const CAN_NOT_VIEW_IVP_NAME = 'can-not-view-IVP';
    const CAN_NOT_VIEW_VPS_NAME = 'can-not-view-VPS';
    const CAN_NOT_VIEW_MEETINGS_NAME = 'can-not-view-Meetings';
    const CAN_NOT_VIEW_KPE_NAME = 'can-not-view-KPE';
    const CAN_NOT_VIEW_SPG_NAME = 'can-not-view-SPG';

    // Dashboard
    const CAN_VIEW_USERS_NAME = 'can-view-users';
    const CAN_VIEW_DIFFERENTS_NAME = 'can-view-differents';
    const CAN_VIEW_ROLES_NAME = 'can-view-roles';

    const CAN_NOT_VIEW_USERS_NAME = 'can-not-view-users';
    const CAN_NOT_VIEW_DIFFERENTS_NAME = 'can-not-view-differents';

    /*
    |--------------------------------------------------------------------------
    | Edit tables permissions
    |--------------------------------------------------------------------------
    */

    // Main
    const CAN_EDIT_EPP_NAME = 'can-edit-EPP';
    const CAN_EDIT_KVPP_NAME = 'can-edit-KVPP';
    const CAN_EDIT_IVP_NAME = 'can-edit-IVP';
    const CAN_EDIT_VPS_NAME = 'can-edit-VPS';
    const CAN_EDIT_MEETINGS_NAME = 'can-edit-Meetings';
    const CAN_EDIT_SPG_NAME = 'can-edit-SPG';

    const CAN_NOT_EDIT_ANYTHING_NAME = 'can-not-edit-anything';
    const CAN_NOT_EDIT_EPP_NAME = 'can-not-edit-EPP';
    const CAN_NOT_EDIT_KVPP_NAME = 'can-not-edit-KVPP';
    const CAN_NOT_EDIT_IVP_NAME = 'can-not-edit-IVP';
    const CAN_NOT_EDIT_VPS_NAME = 'can-not-edit-VPS';
    const CAN_NOT_EDIT_MEETINGS_NAME = 'can-not-edit-Meetings';
    const CAN_NOT_EDIT_SPG_NAME = 'can-not-edit-SPG';

    // Dashboard
    const CAN_EDIT_USERS_NAME = 'can-edit-users';
    const CAN_EDIT_DIFFERENTS_NAME = 'can-edit-differents';

    const CAN_NOT_EDIT_USERS_NAME = 'can-not-edit-users';
    const CAN_NOT_EDIT_DIFFERENTS_NAME = 'can-not-edit-differents';

    /*
    |--------------------------------------------------------------------------
    | Other  permissions
    |--------------------------------------------------------------------------
    */

    const CAN_EXPORT_AS_EXCEL_NAME = 'can-export-as-excel';
    const CAN_EXPORT_UNLIMITED_RECORDS_AS_EXCEL_NAME = 'can-export-unlimited-records-as-excel';
    const CAN_DELETE_FROM_TRASH_NAME = 'can-delete-from-trash';
    const CAN_EDIT_COMMENTS_NAME = 'can-edit-comments';
    const CAN_ADD_PROCESSES_TO_SPG_NAME = 'can-add-processes-to-SPG';
    const CAN_EDIT_PROCESSES_STATUS_HISTORY_NAME = 'can-edit-processes-status-history';
    const CAN_VIEW_KVPP_COINCIDENT_PROCESSES_NAME = 'can-view-kvpp-coincident-processes';
    const CAN_VIEW_KPE_EXTENDED_VERSION_NAME = 'can-view-kpe-extended-version';
    const CAN_VIEW_ALL_ANALYSTS_PROCESSES_NAME = 'can-view-all-analysts-processes';
    const CAN_UPGRADE_PROCESS_STATUS_AFTER_CONTRACT_NAME = 'can-upgrade-process-status-after-contract';

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
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
