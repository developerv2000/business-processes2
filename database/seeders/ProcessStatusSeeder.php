<?php

namespace Database\Seeders;

use App\Models\ProcessStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Вб', 'SВб', 'ПО', 'SПО', 'АЦ', 'SАЦ', 'СЦ', 'SСЦ', 'ПцКк', 'SПцКк', 'Кк', 'SКк', 'ПцКД', 'SКД', 'ПцР', 'SПцР', 'Р', 'P-', 'Зя', 'Отмена'];
        $general_status_id = [1, 1, 2, 2, 3, 3, 4, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 10];

        for ($i = 0; $i < count($name); $i++) {
            $item = new ProcessStatus();
            $item->name = $name[$i];
            $item->general_status_id = $general_status_id[$i];
            $item->save();
        }
    }
}
