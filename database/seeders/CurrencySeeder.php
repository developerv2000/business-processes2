<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['USD', 'EUR', 'RUB', 'INR'];
        $usd_ratio = [1, 1.094, 0.011, 0.012];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Currency();
            $item->name = $name[$i];
            $item->usd_ratio = $usd_ratio[$i];
            $item->save();
        }
    }
}
