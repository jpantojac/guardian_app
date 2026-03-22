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
        $admin = User::where('role', 'admin')->first();
        $users = collect([$user, $admin])->filter();

        // Bogotá Bounding Box
        $minLat = 4.45;
        $maxLat = 4.85;
        $minLng = -74.25;
        $maxLng = -73.95;

        // Generate 1000 incidents over the last 2 years for historical data testing
        for ($i = 0; $i < 1000; $i++) {
            $lat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
            $lng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);

            $category = $categories->random();

            if ($i < 200) {
                // Last 30 days
                $date = now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));
            } else {
                // Older than 30 days, up to 2 years (730 days)
                $date = now()->subDays(rand(31, 730))->subMinutes(rand(0, 1440));
            }

            Incident::create([
                'user_id' => $users->random()->id,
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
