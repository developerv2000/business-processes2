<?php

namespace Database\Seeders;

use App\Models\KvppSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KvppSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['ye', 'yye', 'y'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new KvppSource();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
