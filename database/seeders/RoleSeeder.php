<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Administrator
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::ADMINISTRATOR_NAME;
        $role->save();

        /*
        |--------------------------------------------------------------------------
        | Moderator
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::MODERATOR_NAME;
        $role->save();

        $role->permissions()->attach([
            // View
            Permission::findByName(Permission::CAN_VIEW_EPP_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_KVPP_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_IVP_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_VPS_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_MEETINGS_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_KPE_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_SPG_NAME)->id,

            // Edit
            Permission::findByName(Permission::CAN_EDIT_EPP_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_KVPP_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_IVP_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_VPS_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_MEETINGS_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_SPG_NAME)->id,

            // Other
            Permission::findByName(Permission::CAN_EXPORT_AS_EXCEL_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_COMMENTS_NAME)->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Analyst
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::ANALYST_NAME;
        $role->save();

        /*
        |--------------------------------------------------------------------------
        | BDM
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::BDM_NAME;
        $role->save();

        /*
        |--------------------------------------------------------------------------
        | Inactive
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::INACTIVE_NAME;
        $role->save();

        /*
        |--------------------------------------------------------------------------
        | Guest
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::GUEST_NAME;
        $role->save();

        $role->permissions()->attach([
            Permission::findByName(Permission::CAN_VIEW_EPP_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_KVPP_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_IVP_NAME)->id,
            // Permission::findByName(Permission::CAN_VIEW_VPS_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_MEETINGS_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_KPE_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_SPG_NAME)->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logistician
        |--------------------------------------------------------------------------
        */

        $role = new Role();
        $role->name = Role::LOGISTICIAN_NAME;
        $role->save();

        $role->permissions()->attach([
            Permission::findByName(Permission::CAN_VIEW_APPLICATIONS_NAME)->id,
            Permission::findByName(Permission::CAN_VIEW_ORDERS_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_APPLICATIONS_NAME)->id,
            Permission::findByName(Permission::CAN_EDIT_ORDERS_NAME)->id,
        ]);
    }
}
