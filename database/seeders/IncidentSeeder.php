<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $user = User::where('role', 'user')->first();

        // Bogotá Bounding Box
        $minLat = 4.45;
        $maxLat = 4.85;
        $minLng = -74.25;
        $maxLng = -73.95;

        // Generate incidents with different timestamps for testing filters
        for ($i = 0; $i < 150; $i++) {
            $lat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
            $lng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);

            $category = $categories->random();

            // Distribute incidents across different time periods for filter testing
            if ($i < 30) {
                // Last hour
                $date = now()->subMinutes(rand(1, 60));
            } elseif ($i < 70) {
                // Last 6 hours
                $date = now()->subHours(rand(1, 6));
            } elseif ($i < 120) {
                // Last 24 hours
                $date = now()->subHours(rand(6, 24));
            } else {
                // Older than 24 hours
                $date = now()->subDays(rand(2, 30));
            }

            Incident::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'description' => 'Reporte de prueba generado automáticamente.',
                'location' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)"),
                'status' => 'reported',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
