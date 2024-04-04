<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['I', 'II', 'III', 'IVA', 'IVB'];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Zone();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
