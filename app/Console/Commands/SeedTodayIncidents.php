<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SeedTodayIncidents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guardian:seed-today {--count=50 : La cantidad de incidentes a simular}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inyecta incidentes demo en tiempo real distribuidos orgánicamente sobre el día de hoy, sin borrar el histórico.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = (int) $this->option('count');

        $categories = Category::all();
        // Obtener posibles reportadores (al menos el usuario de prueba)
        $users = User::whereIn('role', ['user', 'admin'])->get();

        // Hotspots densos simulados en Bogotá
        $hotspots = [
            ['lat' => 4.5981, 'lng' => -74.0758], // Centro
            ['lat' => 4.7410, 'lng' => -74.0840], // Suba
            ['lat' => 4.6300, 'lng' => -74.1500], // Kennedy
            ['lat' => 4.6100, 'lng' => -74.1800], // Bosa
            ['lat' => 4.6950, 'lng' => -74.0300], // Usaquén
        ];

        // Bogotá Bounding Box (para incidentes dispersos o ruido de fondo)
        $minLat = 4.45;
        $maxLat = 4.85;
        $minLng = -74.25;
        $maxLng = -73.95;

        $this->info("Simulando la inyección en vivo de {$amount} incidentes de hoy...");
        $bar = $this->output->createProgressBar($amount);

        for ($i = 0; $i < $amount; $i++) {
            // Distribuir: 80% ocurren en Hotspots, 20% dispersos en ruido de fondo
            if (mt_rand(1, 100) <= 80) {
                $spot = $hotspots[array_rand($hotspots)];
                // Desviación estándar (aproximada campana Gaussiana) de offset: +- 1-2km
                $lat = $spot['lat'] + (mt_rand(-200, 200) / 10000);
                $lng = $spot['lng'] + (mt_rand(-200, 200) / 10000);
            } else {
                // Caos aleatorio por el polígono
                $lat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
                $lng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);
            }

            $category = $categories->random();
            // Incidentes creados orgánicamente dentro de las últimas 1 a 3 horas del día de hoy
            $date = now()->subMinutes(rand(0, 180)); 

            $incident = Incident::create([
                'user_id' => $users->random() ? $users->random()->id : 1, 
                'category_id' => $category ? $category->id : 1, 
                'description' => 'Reporte en vivo inyectado por consola durante la demo.',
                'location' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)"),
                'status' => 'reported',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Asignar geolocalización automatica con intersección espacial
            $incident->assignLocalidad();
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("¡Inyección exitosa! La gráfica y el HeatMap subirán automáticamente al recargar.");
    }
}
