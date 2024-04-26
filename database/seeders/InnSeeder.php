<?php

namespace Database\Seeders;

use App\Models\Inn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['ABIES SIBIRICA + EUCALYPTUS GLOBULUS', 'ACER NEGUNDO', 'ALBUMIN + APIS MELLIFICA', 'BROMO-D-CAMPHOR', 'BRYONIA ALBA + CAMPHOR + MAGNESIUM', 'BUDESONIDE + FORMOTEROL'];

        foreach ($names as $name) {
            $instance = new Inn();
            $instance->name = $name;
            $instance->save();
        }
    }
}
