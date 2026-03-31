<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        // Coordenadas dentro de Bogotá D.C.
        $lat = $this->faker->randomFloat(6, 4.48, 4.83);
        $lng = $this->faker->randomFloat(6, -74.22, -73.99);

        return [
            'user_id'              => User::factory(),
            'category_id'          => Category::factory(),
            'description'          => $this->faker->sentence(10),
            'location'             => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)"),
            'location_description' => $this->faker->address(),
            'localidad_id'         => null,
            'privacy_level'        => $this->faker->randomElement(['ANONYMOUS', 'IDENTIFIED']),
            'status'               => 'reported',
            'allow_comments'       => true,
        ];
    }

    public function anonymous(): static
    {
        return $this->state(['privacy_level' => 'ANONYMOUS']);
    }

    public function identified(): static
    {
        return $this->state(['privacy_level' => 'IDENTIFIED']);
    }
}
