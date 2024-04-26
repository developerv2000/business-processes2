<?php

namespace Database\Seeders;

use App\Models\ProductForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parents
        $parents = ['AMPOULES', 'BATHS', 'CAPSULES', 'CARTRIDGE/PENS', 'CREAMS', 'GASES', 'GEL/SOL', 'INFUSIONS', 'LIQUIDS', 'MED.DRESSINGS', 'MEDICAL AIDS', 'OINTMENTS', 'OTHER FORMS', 'P-F SYRINGES', 'POWDER/GRANULE', 'PRESS AEROSOLS', 'SPEC.SOL.FORMS', 'SUPPOSITORIES', 'TABLETS', 'TEAS', 'VIALS'];

        foreach ($parents as $parent) {
            $instance = new ProductForm();
            $instance->name = $parent;
            $instance->save();
        }

        // Childs
        $childs = [
            array(
                'name' => 'AMP INS',
                'parent_id' => 1,
            ),
            array(
                'name' => 'AMP PWD',
                'parent_id' => 1,
            ),
            array(
                'name' => 'IM-PWD-RT',
                'parent_id' => 1,
            ),
            array(
                'name' => 'IV-AMP',
                'parent_id' => 1,
            ),
            array(
                'name' => 'BATH-OIL',
                'parent_id' => 2,
            ),
            array(
                'name' => 'PART BATH',
                'parent_id' => 2,
            ),
            array(
                'name' => 'CAP-CACHET',
                'parent_id' => 3,
            ),
            array(
                'name' => 'ORAL TOP CAP',
                'parent_id' => 3,
            ),
            array(
                'name' => 'OTH CAP',
                'parent_id' => 3,
            ),
            array(
                'name' => 'TC-TAB CT',
                'parent_id' => 19,
            ),
            array(
                'name' => 'TC-TAB GC',
                'parent_id' => 19,
            ),
            array(
                'name' => 'TC-TAB RT',
                'parent_id' => 19,
            ),
            array(
                'name' => 'COMB CREAM',
                'parent_id' => 5,
            ),
            array(
                'name' => 'COMB D-CREAM',
                'parent_id' => 5,
            ),
            array(
                'name' => 'VG-CRM-U',
                'parent_id' => 5,
            ),
            array(
                'name' => 'SYS GAS',
                'parent_id' => 6,
            ),
            array(
                'name' => 'COMB GEL',
                'parent_id' => 7,
            ),
            array(
                'name' => 'EY-GEL',
                'parent_id' => 7,
            ),
            array(
                'name' => 'EY-GEL DR',
                'parent_id' => 7,
            ),
            array(
                'name' => 'EY-GEL-U',
                'parent_id' => 7,
            ),
            array(
                'name' => 'VG-GEL',
                'parent_id' => 7,
            ),
            array(
                'name' => 'VG-GEL-U',
                'parent_id' => 7,
            ),
            array(
                'name' => 'AMP INF',
                'parent_id' => 8,
            ),
            array(
                'name' => 'BAG INF',
                'parent_id' => 8,
            ),
        ];

        foreach ($childs as $child) {
            $instance = new ProductForm();
            $instance->name = $child['name'];
            $instance->parent_id = $child['parent_id'];
            $instance->save();
        }
    }
}
