<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id'); // Keep backward compat temporarily if needed
        $selectedCategories = $request->input('categories', []);
        
        if ($categoryId && empty($selectedCategories)) {
            $selectedCategories = [$categoryId];
        }

        $query = Incident::query();

        if ($year) {
            $query->whereYear('created_at', $year);
        }
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Stats
        $totalIncidents = (clone $query)->count();
        $incidentsToday = (clone $query)->whereDate('created_at', Carbon::today())->count();
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        
        // Incidents by category 
        $incidentsByCategory = \App\Models\Category::withCount(['incidents' => function($q) use ($year, $month, $startDate, $endDate, $selectedCategories) {
            if ($year) $q->whereYear('created_at', $year);
            if ($month) $q->whereMonth('created_at', $month);
            if ($startDate) $q->whereDate('created_at', '>=', $startDate);
            if ($endDate) $q->whereDate('created_at', '<=', $endDate);
            if (!empty($selectedCategories)) $q->whereIn('category_id', $selectedCategories);
        }])
        ->get()
        ->filter(function ($category) {
            return $category->incidents_count > 0;
        })
        ->values()
        ->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->incidents_count,
                'color' => $category->color
            ];
        });
            
        // Incidents Trend Chart
        $trendQuery = clone $query;
        
        if (!$month && !$startDate && !$endDate) {
            // Group by month (shows evolution across the selected year, or all history if year is "Todos")
            $incidentsTrend = $trendQuery->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as date"), 
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } else {
            // Group by day for specific month or specific start/end dates
            $incidentsTrend = $trendQuery->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        }

        // Crime Clock (Incidents by Hour)
        $hourlyCounts = (clone $query)->select(
            DB::raw('CAST(EXTRACT(HOUR FROM created_at) AS INTEGER) as hour'),
            DB::raw('count(*) as count')
        )
        ->groupBy('hour')
        ->pluck('count', 'hour');

        $incidentsByHour = collect();
        for ($i = 0; $i < 24; $i++) {
            $incidentsByHour->push([
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                'count' => $hourlyCounts->get($i, 0)
            ]);
        }

        // Top Localidades
        $topLocalidades = (clone $query)->select('localidad_id', DB::raw('count(*) as count'))
            ->whereNotNull('localidad_id')
            ->groupBy('localidad_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('localidad:id,nombre')
            ->get()
            ->map(function ($incident) {
                return [
                    'name' => $incident->localidad ? $incident->localidad->nombre : 'Desconocida',
                    'count' => $incident->count
                ];
            });

        // Available years for filter
        $availableYears = Incident::selectRaw('EXTRACT(YEAR FROM created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(function($val) { return floor($val); }); // clean pgsql double

        $categories = \App\Models\Category::all();

        return view('admin.dashboard.index', compact(
            'totalIncidents', 
            'incidentsToday', 
            'totalUsers', 
            'activeUsers',
            'incidentsByCategory',
            'incidentsTrend',
            'incidentsByHour',
            'topLocalidades',
            'availableYears',
            'categories',
            'year',
            'month',
            'startDate',
            'endDate',
            'selectedCategories'
        ));
    }
}
