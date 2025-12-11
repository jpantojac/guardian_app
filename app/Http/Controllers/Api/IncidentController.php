<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Zone;
use App\Models\ZoneAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
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

    public function show(Incident $incident)
    {
        return $incident->load(['category', 'user:id,name']);
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
