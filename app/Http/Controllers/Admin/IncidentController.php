<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    /**
     * List all incidents for the admin panel.
     */
    public function index(Request $request)
    {
        $query = Incident::with(['category', 'user']);

        // Filtrado por estado
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Búsqueda de texto
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location_description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $incidents = $query->latest('incident_date')->paginate(15)->withQueryString();

        return view('admin.incidents.index', compact('incidents'));
    }

    /**
     * Update the status of an incident.
     */
    public function updateStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'status' => 'required|in:reported,verified,rejected'
        ]);

        $incident->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Estado actualizado exitosamente.',
            'status' => $incident->status
        ]);
    }
}
