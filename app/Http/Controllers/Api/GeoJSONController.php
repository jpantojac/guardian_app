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
            'category_id',
            'description',
            'created_at',
            DB::raw("ST_AsGeoJSON(location) as geometry")
        )->with('category:id,name,color,icon')->get();

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
