<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoIncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();
        $users = collect([$user, $admin])->filter();

        // Hotspots (Centro, Suba, Kennedy, Bosa, Usaquén)
        $hotspots = [
            ['lat' => 4.5981, 'lng' => -74.0758], // Centro
            ['lat' => 4.7410, 'lng' => -74.0840], // Suba
            ['lat' => 4.6300, 'lng' => -74.1500], // Kennedy
            ['lat' => 4.6100, 'lng' => -74.1800], // Bosa
            ['lat' => 4.6950, 'lng' => -74.0300], // Usaquén
        ];

        // Bogotá Bounding Box (para incidentes dispersos)
        $minLat = 4.45;
        $maxLat = 4.85;
        $minLng = -74.25;
        $maxLng = -73.95;

        // Limpiar para evitar ruido azul uniforme
        DB::statement('TRUNCATE incidents RESTART IDENTITY CASCADE');

        // Cantidad de incidentes a crear agrupados
        $amount = 2500; 

        $this->command->info("Generando {$amount} incidentes agrupados en hotspots para la demo...");

        for ($i = 0; $i < $amount; $i++) {
            // 80% en hotspots, 20% dispersos por toda la ciudad
            if (mt_rand(1, 100) <= 80) {
                $spot = $hotspots[array_rand($hotspots)];
                // Offset cluster: +- 0.02 grados (~2km) de dispersion
                $lat = $spot['lat'] + (mt_rand(-200, 200) / 10000);
                $lng = $spot['lng'] + (mt_rand(-200, 200) / 10000);
            } else {
                // Dispersión aleatoria por toda la ciudad (ruido de fondo)
                $lat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
                $lng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);
            }

            $category = $categories->random();

            // 1500 incidentes en las ultimas 24 horas (pico actual de la demo)
            // 1000 incidentes esparcidos historicos del ultimo año
            if ($i < 1500) {
                $date = now()->subMinutes(rand(0, 1440));
            } else {
                $date = now()->subDays(rand(2, 365));
            }

            $incident = Incident::create([
                'user_id' => $users->random() ? $users->random()->id : 1, // fallback if empty
                'category_id' => $category ? $category->id : 1, // fallback if empty
                'description' => 'Reporte de prueba para demo (generado en las últimas 24h).',
                'location' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)"),
                'status' => 'reported',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $incident->assignLocalidad();
        }

        $this->command->info('¡Incidentes de demo generados con éxito!');
    }
}
