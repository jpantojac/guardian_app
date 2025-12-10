<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;

        $incident = new Incident();
        $incident->user_id = auth()->id();
        $incident->category_id = $request->category_id;
        $incident->description = $request->description;
        $incident->location = DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)");
        $incident->status = 'reported';
        $incident->save();

        return redirect('/')->with('success', 'Incidente reportado exitosamente');
    }
}
