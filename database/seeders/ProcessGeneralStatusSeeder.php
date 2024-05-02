<?php

namespace Database\Seeders;

use App\Models\ProcessGeneralStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessGeneralStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['1ВП', '2ПО', '3АЦ', '4СЦ', '5Кк', '6КД', '7НПР', '8Р', '9Зя', '10Отмена'];
        $name_for_analysts = ['1ВП', '2ПО', '3АЦ', '4СЦ', '5Кк', '5Кк', '5Кк', '5Кк', '5Кк', '5Кк'];
        $stage = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        for ($i = 0; $i < count($name); $i++) {
            $item = new ProcessGeneralStatus();
            $item->name = $name[$i];
            $item->name_for_analysts = $name_for_analysts[$i];
            $item->stage = $stage[$i];
            $item->save();
        }
    }
}
