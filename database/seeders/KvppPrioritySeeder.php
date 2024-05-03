<?php

namespace Database\Seeders;

use App\Models\KvppPriority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KvppPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['A', 'B', 'C'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new KvppPriority();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
