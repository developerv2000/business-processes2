<?php

namespace Database\Seeders;

use App\Models\Payer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Astra Logistics', 'Orthos', 'Dameliz', 'Dusti Farma', 'Asia Farm', 'Moraine Business'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new Payer();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
