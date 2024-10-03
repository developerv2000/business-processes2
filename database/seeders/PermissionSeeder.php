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
            Permission::CAN_EDIT_USERS_NAME,
            Permission::CAN_EDIT_SECONDARY_TABLES_NAME,
            Permission::CAN_NOT_DOWNLOAD_NAME,
            Permission::CAN_NOT_EDIT_NAME,
            Permission::CAN_NOT_EDIT_EPP_NAME,
            Permission::CAN_NOT_EDIT_IVP_NAME,
            Permission::CAN_NOT_EDIT_VPS_NAME,
            Permission::CAN_NOT_EDIT_MEETINGS_NAME,
            Permission::CAN_NOT_VIEW_EPP_NAME,
            Permission::CAN_NOT_VIEW_KVPP_NAME,
            Permission::CAN_NOT_VIEW_IVP_NAME,
            Permission::CAN_NOT_VIEW_VPS_NAME,
            Permission::CAN_NOT_VIEW_MEETINGS_NAME,
        ];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Permission();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
