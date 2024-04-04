<?php

namespace Database\Seeders;

use App\Models\ManufacturerBlacklist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufacturerBlacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            'No contact info',
            'No interesting Pd',
            'Markets',
            'Prices',
            'License Fee',
            'Business Model',
            'Dossier and other docs',
            'CDMO',
            'API MFG',
        ];

        for ($i = 0; $i < count($name); $i++) {
            $item = new ManufacturerBlacklist();
            $item->name = $name[$i];
            $item->save();
        }
    }
}
