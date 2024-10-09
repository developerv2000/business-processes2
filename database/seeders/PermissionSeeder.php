<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            Permission::CAN_VIEW_EPP_NAME,
            Permission::CAN_VIEW_KVPP_NAME,
            Permission::CAN_VIEW_IVP_NAME,
            Permission::CAN_VIEW_VPS_NAME,
            Permission::CAN_VIEW_MEETINGS_NAME,
            Permission::CAN_VIEW_KPE_NAME,
            Permission::CAN_VIEW_SPG_NAME,
            Permission::CAN_NOT_VIEW_EPP_NAME,
            Permission::CAN_NOT_VIEW_KVPP_NAME,
            Permission::CAN_NOT_VIEW_IVP_NAME,
            Permission::CAN_NOT_VIEW_VPS_NAME,
            Permission::CAN_NOT_VIEW_MEETINGS_NAME,
            Permission::CAN_NOT_VIEW_KPE_NAME,
            Permission::CAN_NOT_VIEW_SPG_NAME,
            Permission::CAN_VIEW_USERS_NAME,
            Permission::CAN_VIEW_DIFFERENTS_NAME,
            Permission::CAN_VIEW_ROLES_NAME,
            Permission::CAN_NOT_VIEW_USERS_NAME,
            Permission::CAN_NOT_VIEW_DIFFERENTS_NAME,
            Permission::CAN_EDIT_EPP_NAME,
            Permission::CAN_EDIT_KVPP_NAME,
            Permission::CAN_EDIT_IVP_NAME,
            Permission::CAN_EDIT_VPS_NAME,
            Permission::CAN_EDIT_MEETINGS_NAME,
            Permission::CAN_EDIT_SPG_NAME,
            Permission::CAN_NOT_EDIT_EPP_NAME,
            Permission::CAN_NOT_EDIT_KVPP_NAME,
            Permission::CAN_NOT_EDIT_IVP_NAME,
            Permission::CAN_NOT_EDIT_VPS_NAME,
            Permission::CAN_NOT_EDIT_MEETINGS_NAME,
            Permission::CAN_NOT_EDIT_SPG_NAME,
            Permission::CAN_EDIT_USERS_NAME,
            Permission::CAN_EDIT_DIFFERENTS_NAME,
            Permission::CAN_NOT_EDIT_USERS_NAME,
            Permission::CAN_NOT_EDIT_DIFFERENTS_NAME,
            Permission::CAN_EXPORT_AS_EXCEL_NAME,
            Permission::CAN_NOT_EXPORT_AS_EXCEL_NAME,
            Permission::CAN_EXPORT_UNLIMITED_RECORDS_AS_EXCEL_NAME,
            Permission::CAN_VIEW_ALL_ANALYSTS_PROCESSES_NAME,
            Permission::CAN_EDIT_ALL_ANALYSTS_PROCESSES_NAME,
            Permission::CAN_DELETE_FROM_TRASH_NAME,
            Permission::CAN_EDIT_COMMENTS_NAME,
            Permission::CAN_ADD_PROCESSES_TO_SPG_NAME,
            Permission::CAN_EDIT_PROCESSES_STATUS_HISTORY_NAME,
            Permission::CAN_VIEW_KVPP_COINCIDENT_PROCESSES_NAME,
            Permission::CAN_VIEW_KPE_EXTENDED_VERSION_NAME,
            Permission::CAN_UPGRADE_PROCESS_STATUS_AFTER_CONTRACT_NAME,
        ];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Permission();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
