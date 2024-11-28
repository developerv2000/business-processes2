<?php

namespace Database\Seeders;

use App\Models\InvoiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Goods', 'Service'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new InvoiceCategory();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
