<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Análisis Espacial (GeoJSON)
 *
 * APIs que exponen datos en formato estándar GeoJSON (RFC 7946) para ser consumidos por mapas.
 */
class GeoJSONController extends Controller
{
    /**
     * Obtener incidentes en GeoJSON.
     * 
     * Retorna una colección de características GeoJSON (`FeatureCollection`) que representan los incidentes registrados en el sistema, ideal para ser consumida directamente por mapas web y sistemas SIG. Permite filtrar los resultados temporalmente o por categoría.
     *
     * @queryParam days int Filtrar incidentes de los últimos N días. Example: 7
     * @queryParam year int Filtrar por año. Example: 2026
     * @queryParam month int Filtrar por mes (1-12). Example: 3
     * @queryParam start_date date Filtrar incidentes ocurridos desde esta fecha de inicio. Example: 2026-03-01
     * @queryParam end_date date Filtrar incidentes ocurridos hasta esta fecha de fin. Example: 2026-03-31
     * @queryParam category_id int Filtrar por ID de la categoría del delito. Example: 1
     * @queryParam categories array Filtrar múltiples IDs de categoría. Example: [1,2]
     * 
     * @response {
     *  "type": "FeatureCollection",
     *  "features": [
     *    {
     *      "type": "Feature",
     *      "geometry": {
     *        "type": "Point",
     *        "coordinates": [-74.0721, 4.711]
     *      },
     *      "properties": {
     *        "id": 1001,
     *        "category": "Hurto a personas",
     *        "color": "#ff0000",
     *        "icon": "hurto_icon",
     *        "description": "Robo con arma blanca en la calle 80",
     *        "location_description": "Cerca a la estación de Transmilenio",
     *        "status": "reported",
     *        "privacy_level": "IDENTIFIED",
     *        "reporter_name": "Juan Perez",
     *        "localidad": "Engativá",
     *        "photos": ["http://guardianapp.test/storage/incident_photos/example.jpg"],
     *        "created_at": "2026-04-01T14:30:00Z"
     *      }
     *    }
     *  ]
     * }
     */
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
        
        // Admin filters support
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
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

    /**
     * Obtener polígonos de localidades (GeoJSON).
     * 
     * Retorna la capa cartográfica de las localidades de la ciudad en formato `FeatureCollection` GeoJSON.
     * Útil para renderizar los límites jurisdiccionales sociodemográficos sobre el mapa principal.
     * 
     * @response {
     *  "type": "FeatureCollection",
     *  "features": [
     *    {
     *      "type": "Feature",
     *      "properties": {
     *        "id": 1,
     *        "nombre": "Usaquén"
     *      },
     *      "geometry": {
     *        "type": "MultiPolygon",
     *        "coordinates": [[[[ -74.012, 4.789 ], [ -74.015, 4.792 ]]]]
     *      }
     *    }
     *  ]
     * }
     */
    public function localidades()
    {
        $localidades = DB::select("SELECT id, nombre, ST_AsGeoJSON(geom) as geometry FROM localidades");

        $features = array_map(function ($loc) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $loc->id,
                    'nombre' => $loc->nombre
                ],
                'geometry' => json_decode($loc->geometry)
            ];
        }, $localidades);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
}
