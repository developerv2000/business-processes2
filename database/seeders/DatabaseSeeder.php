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
            PermissionSeeder::class,
            UserSeeder::class,
            CountrySeeder::class,
            ZoneSeeder::class,
            ManufacturerCategorySeeder::class,
            ManufacturerBlacklistSeeder::class,
            ProductClassSeeder::class,
            ManufacturerSeeder::class,
            InnSeeder::class,
            ProductFormSeeder::class,
            ProductShelfLifeSeeder::class,
            ProductSeeder::class,
            CountryCodeSeeder::class,
            CurrencySeeder::class,
            MarketingAuthorizationHolderSeeder::class,
            ProcessResponsiblePersonSeeder::class,
            ProcessGeneralStatusSeeder::class,
            ProcessStatusSeeder::class,
            ProcessSeeder::class,
            KvppStatusSeeder::class,
            KvppPrioritySeeder::class,
            PortfolioManagerSeeder::class,
            KvppSeeder::class,
            MeetingSeeder::class,
        ]);
    }
}
