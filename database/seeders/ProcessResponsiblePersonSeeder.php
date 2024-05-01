<?php

namespace Database\Seeders;

use App\Models\ProcessResponsiblePerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessResponsiblePersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            'Фариз',
            'Фариз на КК',
            'Худойдод',
            'Худойдод на КК',
            'Дарья',
            'Дарья на КК',
            'Ирини',
            'Ирини на КК',
            'Фирдавс',
            'Фирдавс на КК',
            'Бехруз',
            'Бехруз на КК',
            'Фаррух',
            'Фаррух на КК',
            'Акмал',
            'Азим',
            'Мухаммад',
            'Азиз',
            'Мехродж',
            'Нозим',
            'Джахонгир',
            'Джахонгир на КК',
        ];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new ProcessResponsiblePerson();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
