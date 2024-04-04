<?php

namespace Database\Seeders;

use App\Models\ProductClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['ЛС', 'БАД', 'МИ', 'КОСМ'];

        for ($i = 0; $i < count($name); $i++) {
            $item = new ProductClass();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
