<?php

namespace Database\Seeders;

use App\Models\Kvpp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KvppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kvpp::factory()->count(10)->create();
    }
}
