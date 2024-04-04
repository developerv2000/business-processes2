<?php

namespace Database\Seeders;

use App\Models\ManufacturerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufacturerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['НПП', 'УДС'];

        for ($i = 0; $i < count($name); $i++) {
            $item = new ManufacturerCategory();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
