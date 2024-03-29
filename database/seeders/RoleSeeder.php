<?php

namespace Database\Seeders;

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
        $name = [
            Role::ADMIN_NAME,
            Role::MODERATOR_NAME,
            Role::ANALYST_NAME,
            Role::BDM_NAME,
            Role::ROBOT_NAME,
            Role::TRAINEE_NAME,
        ];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Role();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
