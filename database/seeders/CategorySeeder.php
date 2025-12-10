<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Homicidio', 'slug' => 'homicidio', 'color' => '#dc2626', 'icon' => 'skull'],
            ['name' => 'Hurto a personas', 'slug' => 'hurto-personas', 'color' => '#ea580c', 'icon' => 'user-minus'],
            ['name' => 'Hurto a comercio', 'slug' => 'hurto-comercio', 'color' => '#d97706', 'icon' => 'store-slash'],
            ['name' => 'Hurto a residencias', 'slug' => 'hurto-residencias', 'color' => '#ca8a04', 'icon' => 'home'],
            ['name' => 'Extorsión', 'slug' => 'extorsion', 'color' => '#65a30d', 'icon' => 'money-bill'],
            ['name' => 'Violencia intrafamiliar', 'slug' => 'violencia-intrafamiliar', 'color' => '#7c3aed', 'icon' => 'home-heart'],
            ['name' => 'Lesiones personales', 'slug' => 'lesiones-personales', 'color' => '#db2777', 'icon' => 'band-aid'],
            ['name' => 'Otro', 'slug' => 'otro', 'color' => '#475569', 'icon' => 'question'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
