<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Zone;
use App\Models\ZoneAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Incidentes
 *
 * APIs para gestionar los reportes ciudadanos de incidentes, incluyendo soporte a filtros y geolocalización.
 */
class IncidentController extends Controller
{
    /**
     * Listado paginado de incidentes.
     *
     * Obtiene una lista de los incidentes reportados ordenados desde el más reciente. Este endpoint facilita la revisión de eventos e incluye información básica del reporte y su clasificación de seguridad.
     * 
     * @queryParam category_id int Filtrar por ID de la categoría (tipo de delito). Example: 3
     * @queryParam status string Estado actual del incidente: 'reported', 'investigating', 'resolved', 'dismissed'. Example: reported
     * @queryParam days int Limita los resultados a los últimos N días. Example: 30
     * @queryParam page int Número de página correspondiente de la paginación. Example: 1
     * 
     * @response {
     *  "current_page": 1,
     *  "data": [
     *    {
     *      "id": 1500,
     *      "category_id": 3,
     *      "user_id": 200,
     *      "description": "Se presenció una pelea...",
     *      "status": "reported",
     *      "created_at": "2026-04-02T10:00:00.000000Z",
     *      "category": { "id": 3, "name": "Disturbios", "color": "#f39c12", "icon": "warning" },
     *      "user": { "id": 200, "name": "Usuario Anónimo" }
     *    }
     *  ],
     *  "from": 1,
     *  "last_page": 10,
     *  "per_page": 20,
     *  "to": 20,
     *  "total": 200
     * }
     */
    public function index(Request $request)
    {
        $query = Incident::with(['category', 'user:id,name']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Time filters
        if ($request->has('days')) {
            $query->where('created_at', '>=', now()->subDays($request->days));
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Reportar nuevo incidente.
     *
     * Crea y registra un nuevo evento de seguridad en la base de datos de manera centralizada. 
     * Recibe coordenadas geográficas (latitud, longitud), detalles en texto y recursos multimedia opcionales (fotos). 
     * Tras registrar el incidente, el sistema analiza si el evento detona alguna alerta en la zona.
     * 
     * @authenticated
     * 
     * @bodyParam category_id int required ID de la categoría de delito aplicable al evento. Example: 2
     * @bodyParam description string required Descripción textual y detallada del evento presenciado. Example: Sospechoso intentando abrir vehículo estacionado.
     * @bodyParam latitude float required Latitud de la ubicación del incidente según GPS. Example: 4.6097
     * @bodyParam longitude float required Longitud de la ubicación del incidente según GPS. Example: -74.0817
     * @bodyParam location_description string Descripción adicional para facilitar la localización exacta. Example: Frente al parqueadero norte.
     * @bodyParam privacy_level string required Nivel de privacidad requerido para proteger la identidad del ciudadano ('ANONYMOUS' o 'IDENTIFIED'). Example: ANONYMOUS
     * @bodyParam photos file[] (Opcional) Subida de hasta 3 fotografías como evidencia visual del suceso (Máx. 10MB por archivo).
     * 
     * @response 201 {
     *   "id": 205,
     *   "user_id": 8,
     *   "category_id": 2,
     *   "description": "Sospechoso intentando abrir vehículo estacionado.",
     *   "location_description": "Frente al parqueadero norte.",
     *   "privacy_level": "ANONYMOUS",
     *   "status": "reported",
     *   "created_at": "2026-04-05T21:00:00.000000Z",
     *   "photos": []
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string', // Changed to required as it's usually main content
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_description' => 'nullable|string|max:255',
            'privacy_level' => 'required|in:ANONYMOUS,IDENTIFIED',
            'photos' => 'nullable|array|max:3', // Allow up to 3 photos
            'photos.*' => 'image|max:10240', // 10MB max per photo
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;

        $incident = new Incident();
        $incident->user_id = $request->user()->id;
        $incident->category_id = $request->category_id;
        $incident->description = $request->description;
        $incident->location_description = $request->location_description;
        $incident->privacy_level = $request->privacy_level;
        $incident->location = DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)");
        $incident->status = 'reported';
        $incident->save();

        // Handle Photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('incident_photos', 'public');
                $incident->photos()->create([
                    'photo_path' => $path,
                    'order' => $index + 1,
                ]);
            }
        }

        // Auto-assign Localidad using PostGIS
        $incident->assignLocalidad();

        // Check for alerts
        $this->checkAlerts($incident);

        return response()->json($incident->load('photos'), 201);
    }

    /**
     * Detalles completos del incidente.
     *
     * Este endpoint despliega toda la información en profundidad de un único incidente, incluyendo estadísticas sociales asociadas. Principalmente se utiliza para las vistas de detalle de la aplicación ciudadana para comentar y reaccionar.
     *
     * @urlParam incident int required ID numérico del incidente a consultar. Example: 205
     * 
     * @response {
     *   "id": 205,
     *   "description": "Sospechoso intentando abrir vehículo estacionado.",
     *   "status": "reported",
     *   "privacy_level": "ANONYMOUS",
     *   "social_stats": {
     *      "comments_count": 5,
     *      "reactions_count": 12,
     *      "reaction_types": { "like": 10, "care": 2 }
     *   },
     *   "category": { "id": 2, "name": "Vandalismo/Robo Vehicular" },
     *   "user": { "id": 8, "name": "Carlos Gomez", "profile_photo_path": "profiles/photo.jpg" },
     *   "comments": [],
     *   "photos": []
     * }
     */
    public function show(Incident $incident)
    {
        $incident->load([
            'category',
            'user:id,name,profile_photo_path',
            'comments' => function ($query) {
                $query->whereNull('parent_id')->orderBy('created_at', 'desc');
            },
            'comments.user:id,name,profile_photo_path',
            'comments.reactions',
            'comments.replies.user:id,name,profile_photo_path',
            'comments.replies.reactions',
            'photos'
        ]);

        // Aggregate reactions from all comments (flat list)
        $allReactions = $incident->comments->flatMap(function ($comment) {
            return $comment->reactions->concat(
                $comment->replies->flatMap(fn($reply) => $reply->reactions)
            );
        });

        $reactionSummary = $allReactions->groupBy('type')->map->count();
        $totalReactions = $allReactions->count();

        // Add stats to instance
        $incident->social_stats = [
            'comments_count' => $incident->comments->count() + $incident->comments->sum(fn($c) => $c->replies->count()),
            'reactions_count' => $totalReactions,
            'reaction_types' => $reactionSummary,
        ];

        return response()->json($incident);
    }

    protected function checkAlerts(Incident $incident)
    {
        // Find which zone this incident falls into
        // This requires PostGIS functions
        $zone = Zone::whereRaw("ST_Intersects(boundary, ST_SetSRID(ST_MakePoint(?, ?), 4326))", [$incident->longitude, $incident->latitude])->first();

        if ($zone) {
            // Check alerts for this zone and category
            $alerts = ZoneAlert::where('zone_id', $zone->id)
                ->where('category_id', $incident->category_id)
                ->where('active', true)
                ->get();

            foreach ($alerts as $alert) {
                // Count incidents in time window
                $count = Incident::where('category_id', $incident->category_id)
                    ->whereRaw("ST_Intersects((SELECT boundary FROM zones WHERE id = ?), location)", [$zone->id])
                    ->where('created_at', '>=', now()->subHours($alert->time_window_hours))
                    ->count();

                if ($count >= $alert->threshold) {
                    // Trigger notification (Job)
                    // Dispatch AlertJob::dispatch($alert, $zone);
                }
            }
        }
    }
}
