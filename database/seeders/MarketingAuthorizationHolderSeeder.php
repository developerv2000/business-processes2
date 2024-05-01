<?php

namespace Database\Seeders;

use App\Models\MarketingAuthorizationHolder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketingAuthorizationHolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['S', 'B', 'V', 'T', 'L', 'BO', 'G', 'N', 'Обсуждается'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new MarketingAuthorizationHolder();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
