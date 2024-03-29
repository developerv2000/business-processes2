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
        $name = ['Firdavs Kilichbekov', 'Nuruloev Olimjon', 'Shahriyor Pirov', 'Alim Munavarov', 'Jahongir Nemonov', 'Muzaffar Behruz', 'Darya Rassulova', 'Irini Kouimtzidou', 'Fariz Mirzoev', 'Firdavs Sirojov', 'Khudoydod Sharipov', 'Farrukh Kayumov', 'Nuridinov Bobur'];

        $email = ['firdavs.kilichbekov@evolet.co.uk', 'olimjon.nuruloev@evolet.co.uk', 'shahriyor_p@evolet.co.uk', 'alim.munavarov@evolet.co.uk', ' bdm3@evolet.co.uk', 'behruz.muzaffar@outlook.com', 'bdm1@evolet.co.uk', 'irini@evolet.co.uk', 'farizmirzo@evolet.co.uk', 'firdavs.sirojov@evolet.co.uk', 'khudoydod.sharipov@evolet.co.uk', 'farrukh.kayumov@evolet.co.uk', 'admin@mail.ru'];

        $photo = ['firdavs-kilichbekov.png', 'nuruloev-olimjon.png', 'shahriyor-pirov.png', 'alim-munavarov.png', 'jahongir-nemonov.png', 'muzaffar-behruz.png', 'darya-rassulova.png', 'irini-kouimtzidou.png', 'fariz-mirzoev.png', 'firdavs-sirojov.png', 'khudoydod-sharipov.png', 'farrukh-kayumov.png', 'irini-kouimtzidou.png'];

        $adminID = Role::where('name', Role::ADMIN_NAME)->first()->id;
        $moderatorID = Role::where('name', Role::MODERATOR_NAME)->first()->id;
        $analystID = Role::where('name', Role::ANALYST_NAME)->first()->id;
        $bdmID = Role::where('name', Role::BDM_NAME)->first()->id;
        $robotID = Role::where('name', Role::ROBOT_NAME)->first()->id;
        $traineeID = Role::where('name', Role::TRAINEE_NAME)->first()->id;

        $roleID = [
            [$adminID],
            [$analystID, $traineeID],
            [$analystID],
            [$analystID, $moderatorID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$bdmID, $robotID],
            [$adminID],
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

            $item->loadDefaultSettings();
        }
    }
}
