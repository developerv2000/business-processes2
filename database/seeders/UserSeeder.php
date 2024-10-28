<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Nuruloev Olimjon', 'Shahriyor Pirov', 'Alim Munavarov', 'Muzaffar Behruz', 'Darya Rassulova', 'Irini Kouimtzidou', 'Nuridinov Bobur', 'Guest 01', 'Azimjon Vokhidi'];

        $email = ['olimjon.nuruloev@evolet.co.uk', 'shahriyor_p@evolet.co.uk', 'alim.munavarov@evolet.co.uk', 'behruz.muzaffar@outlook.com', 'bdm1@evolet.co.uk', 'irini@evolet.co.uk', 'developer@mail.com', 'guest@mail.com', 'vokhid.azimjon@evolet.co.uk'];

        $photo = ['nuruloev-olimjon.png', 'shahriyor-pirov.png', 'alim-munavarov.png', 'muzaffar-behruz.png', 'darya-rassulova.png', 'irini-kouimtzidou.png', 'developer.jpg', 'developer.jpg', 'developer.jpg'];

        $adminID = Role::where('name', Role::ADMINISTRATOR_NAME)->first()->id;
        $moderatorID = Role::where('name', Role::MODERATOR_NAME)->first()->id;
        $analystID = Role::where('name', Role::ANALYST_NAME)->first()->id;
        $bdmID = Role::where('name', Role::BDM_NAME)->first()->id;
        $inactiveID = Role::where('name', Role::INACTIVE_NAME)->first()->id;
        $guestID = Role::where('name', Role::GUEST_NAME)->first()->id;
        $logisticianID = Role::where('name', Role::LOGISTICIAN_NAME)->first()->id;

        $roleID = [
            [$moderatorID, $analystID],
            [$moderatorID, $analystID],
            [$moderatorID, $analystID],
            [$bdmID, $inactiveID],
            [$bdmID, $inactiveID],
            [$bdmID, $inactiveID],
            [$adminID],
            [$guestID],
            [$logisticianID],
        ];

        $password = '12345';

        for ($i = 0; $i < count($name); $i++) {
            $item = User::create([
                'name' => $name[$i],
                'email' => $email[$i],
                'photo' => $photo[$i],
                'password' => bcrypt($password),
            ]);

            foreach ($roleID[$i] as $id) {
                $item->roles()->attach($id);
            }

            $item->resetDefaultSettings();
        }
    }
}
