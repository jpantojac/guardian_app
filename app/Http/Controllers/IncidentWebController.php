<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\IncidentPhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncidentWebController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_description' => 'nullable|string|max:500',
            'privacy_level' => 'required|in:ANONYMOUS,IDENTIFIED',
            'evidence_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'captcha' => 'required|integer'
        ]);

        if ($request->captcha != session('captcha_result')) {
            return back()->with('error', 'El resultado de la suma es incorrecto. Inténtalo de nuevo.')->withInput();
        }

        $lat = $request->latitude;
        $lng = $request->longitude;

        DB::beginTransaction();

        try {
            $incident = new Incident();
            $incident->user_id = auth()->id();
            $incident->category_id = $request->category_id;
            $incident->description = $request->description;
            $incident->location = DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)");
            $incident->location_description = $request->location_description;
            $incident->privacy_level = $request->privacy_level;
            $incident->status = 'reported';

            $incident->save();

            // Auto-assign localidad
            $incident->assignLocalidad();

            // Handle photos
            if ($request->hasFile('evidence_photos')) {
                foreach ($request->file('evidence_photos') as $index => $photo) {
                    if (!$photo->isValid()) {
                        Log::error('Invalid photo upload');
                        continue;
                    }

                    $path = $photo->store('evidence_photos', 'public');

                    IncidentPhoto::create([
                        'incident_id' => $incident->id,
                        'photo_path' => $path,
                        'order' => $index + 1
                    ]);
                }
            }

            DB::commit();
            return redirect('/')->with('success', 'Incidente reportado exitosamente');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating incident: ' . $e->getMessage());
            return redirect('/')->with('error', 'Hubo un error al guardar el incidente: ' . $e->getMessage());
        }
    }
}
