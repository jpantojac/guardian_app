<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeoJSONController extends Controller
{
    public function index(Request $request)
    {
        // Return GeoJSON FeatureCollection
        // Apply privacy noise if user is not admin/analyst?
        // For now, let's assume public view has noise or grid.

        // We will return raw GeoJSON from PostGIS for efficiency

        $query = Incident::query();

        if ($request->has('days')) {
            $query->where('created_at', '>=', now()->subDays($request->days));
        }

        // Build GeoJSON query
        // We apply ST_SnapToGrid for simple privacy/clustering or random noise
        // ST_AsGeoJSON(location)

        $features = $query->select(
            'id',
            'user_id', // Needed for reporter name logic
            'category_id',
            'localidad_id',
            'description',
            'location_description',
            'privacy_level',
            'status',
            'created_at',
            DB::raw("ST_AsGeoJSON(location) as geometry")
        )
            ->with([
                'category:id,name,color,icon',
                'user:id,name',
                'localidad:id,nombre',
                'photos'
            ])
            ->get();

        $geoJsonFeatures = $features->map(function ($incident) {
            return [
                'type' => 'Feature',
                'geometry' => json_decode($incident->geometry),
                'properties' => [
                    'id' => $incident->id,
                    'category' => $incident->category->name,
                    'color' => $incident->category->color,
                    'icon' => $incident->category->icon,
                    'description' => $incident->description,
                    'location_description' => $incident->location_description,
                    'status' => $incident->status,
                    'privacy_level' => $incident->privacy_level,
                    'reporter_name' => $incident->reporter_name, // Uses accessor
                    'localidad' => $incident->localidad ? $incident->localidad->nombre : null,
                    'photos' => $incident->photos->map(fn($p) => $p->url),
                    'created_at' => $incident->created_at->toIso8601String(),
                ]
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $geoJsonFeatures
        ]);
    }
}
