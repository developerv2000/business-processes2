<?php

namespace Database\Seeders;

use App\Models\PortfolioManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = ['Акмал', 'Азиз', 'Фирдавс', 'Нозим', 'Сабрина', 'Абдукарим', 'Мехродж', 'Азим'];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new PortfolioManager();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
