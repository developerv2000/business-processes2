<?php

namespace Database\Seeders;

use App\Models\InvoiceItemCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 'Product' and 'Other payments' for invoices of 'Goods' category
        // 'Service' for invoices of 'Service' category
        $name = ['Product', 'Other payments', 'Service'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new InvoiceItemCategory();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
