<?php

namespace Database\Seeders;

use App\Models\ProductShelfLife;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductShelfLifeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['12', '18', '24', '36', '48', '60', 'TBC'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new ProductShelfLife();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
