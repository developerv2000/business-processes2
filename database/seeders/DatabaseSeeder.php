<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CountrySeeder::class,
            ZoneSeeder::class,
            ManufacturerCategorySeeder::class,
            ManufacturerBlacklistSeeder::class,
            ProductClassSeeder::class,
            ManufacturerSeeder::class,
        ]);
    }
}
