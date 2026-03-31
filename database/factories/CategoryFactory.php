<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Hurto', 'Vandalismo', 'ViolenciaFamiliar',
            'DelitosS', 'Extorsion', 'Homicidio',
            'Lesiones', 'Fraude',
        ]);

        return [
            'name'        => $name,
            'slug'        => \Illuminate\Support\Str::slug($name . '-' . $this->faker->unique()->numberBetween(1, 9999)),
            'description' => $this->faker->sentence(),
            'color'       => $this->faker->hexColor(),
            'icon'        => $this->faker->word(),
        ];
    }
}
