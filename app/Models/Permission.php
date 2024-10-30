<?php

namespace App\Models;

use App\Support\Traits\FindsRecordByName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    use FindsRecordByName;

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | View tables permissions
    |--------------------------------------------------------------------------
    */

    // Analysts department
    const CAN_VIEW_EPP_NAME = 'can view EPP';
    const CAN_VIEW_KVPP_NAME = 'can view KVPP';
    const CAN_VIEW_IVP_NAME = 'can view IVP';
    const CAN_VIEW_VPS_NAME = 'can view VPS';
    const CAN_VIEW_MEETINGS_NAME = 'can view Meetings';
    const CAN_VIEW_KPE_NAME = 'can view KPE';
    const CAN_VIEW_SPG_NAME = 'can view SPG';

    const CAN_NOT_VIEW_EPP_NAME = 'can`t view EPP';
    const CAN_NOT_VIEW_KVPP_NAME = 'can`t view KVPP';
    const CAN_NOT_VIEW_IVP_NAME = 'can`t view IVP';
    const CAN_NOT_VIEW_VPS_NAME = 'can`t view VPS';
    const CAN_NOT_VIEW_MEETINGS_NAME = 'can`t view Meetings';
    const CAN_NOT_VIEW_KPE_NAME = 'can`t view KPE';
    const CAN_NOT_VIEW_SPG_NAME = 'can`t view SPG';

    // Logistics department
    const CAN_VIEW_PROCESSES_FOR_ORDER_NAME = 'can view Processes for order';
    const CAN_VIEW_ORDERS_NAME = 'can view Orders';

    const CAN_NOT_VIEW_PROCESSES_FOR_ORDER_NAME = 'can`t view Processes for order';
    const CAN_NOT_VIEW_ORDERS_NAME = 'can`t view Orders';

    // Dashboard
    const CAN_VIEW_USERS_NAME = 'can view users';
    const CAN_VIEW_DIFFERENTS_NAME = 'can view differents';
    const CAN_VIEW_ROLES_NAME = 'can view roles';

    const CAN_NOT_VIEW_USERS_NAME = 'can`t view users';
    const CAN_NOT_VIEW_DIFFERENTS_NAME = 'can`t view differents';

    /*
    |--------------------------------------------------------------------------
    | Edit tables permissions
    |--------------------------------------------------------------------------
    */

    // Analysts department
    const CAN_EDIT_EPP_NAME = 'can edit EPP';
    const CAN_EDIT_KVPP_NAME = 'can edit KVPP';
    const CAN_EDIT_IVP_NAME = 'can edit IVP';
    const CAN_EDIT_VPS_NAME = 'can edit VPS';
    const CAN_EDIT_MEETINGS_NAME = 'can edit Meetings';
    const CAN_EDIT_SPG_NAME = 'can edit SPG';

    const CAN_NOT_EDIT_EPP_NAME = 'can`t edit EPP';
    const CAN_NOT_EDIT_KVPP_NAME = 'can`t edit KVPP';
    const CAN_NOT_EDIT_IVP_NAME = 'can`t edit IVP';
    const CAN_NOT_EDIT_VPS_NAME = 'can`t edit VPS';
    const CAN_NOT_EDIT_MEETINGS_NAME = 'can`t edit Meetings';
    const CAN_NOT_EDIT_SPG_NAME = 'can`t edit SPG';

    // Logistics department
    const CAN_EDIT_PROCESSES_FOR_ORDER_NAME = 'can edit Processes for order';
    const CAN_EDIT_ORDERS_NAME = 'can edit Orders';

    const CAN_NOT_EDIT_PROCESSES_FOR_ORDER_NAME = 'can`t edit Processes for order';
    const CAN_NOT_EDIT_ORDERS_NAME = 'can`t edit Orders';

    // Dashboard
    const CAN_EDIT_USERS_NAME = 'can edit users';
    const CAN_EDIT_DIFFERENTS_NAME = 'can edit differents';

    const CAN_NOT_EDIT_USERS_NAME = 'can`t edit users';
    const CAN_NOT_EDIT_DIFFERENTS_NAME = 'can`t edit differents';

    /*
    |--------------------------------------------------------------------------
    | Other permissions
    |--------------------------------------------------------------------------
    */

    const CAN_DELETE_FROM_TRASH_NAME = 'can delete from trash';
    const CAN_EDIT_COMMENTS_NAME = 'can edit comments';

    // Export
    const CAN_EXPORT_AS_EXCEL_NAME = 'can export as excel';
    const CAN_NOT_EXPORT_AS_EXCEL_NAME = 'can`t export as excel';
    const CAN_EXPORT_UNLIMITED_RECORDS_AS_EXCEL_NAME = 'can export unlimited records as excel';

    // KVPP
    const CAN_VIEW_KVPP_COINCIDENT_PROCESSES_NAME = 'can view kvpp coincident processes';

    // KPE
    const CAN_VIEW_KPE_EXTENDED_VERSION_NAME = 'can view KPE extended version';
    const CAN_VIEW_KPE_OF_ALL_ANALYSTS = 'can view KPE of all analysts';

    // SPG
    const CAN_CONTROL_SPG_PROCESSES = 'can control SPG processes';

    // VPS
    const CAN_VIEW_ALL_ANALYSTS_PROCESSES_NAME = 'can view all analysts processes';
    const CAN_EDIT_ALL_ANALYSTS_PROCESSES_NAME = 'can edit all analysts processes';
    const CAN_EDIT_PROCESSES_STATUS_HISTORY_NAME = 'can edit processes status history';
    const CAN_UPGRADE_PROCESS_STATUS_AFTER_CONTRACT_NAME = 'can upgrade process status after contract';
    const CAN_RECIEVE_NOTIFICATION_ON_PROCESS_CONTRACT = 'can recieve notification on process contract';
    const CAN_MARK_PROCESS_AS_READY_FOR_ORDER = 'can mark process as ready for order';

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

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Helper function to get the denying permission name.
     *
     * If the requested permission is 'CAN_EXPORT', this function returns 'CAN_NOT_EXPORT'.
     */
    public static function getDenyingPermission($permissionName)
    {
        // Swap 'can' with 'can`t' to get the denying permission name
        return 'can`t ' . substr($permissionName, 4);
    }
}
