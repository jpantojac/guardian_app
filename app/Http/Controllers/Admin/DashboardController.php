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
            $query->whereYear('incident_date', $year);
        }
        if ($month) {
            $query->whereMonth('incident_date', $month);
        }
        if ($startDate) {
            $query->whereDate('incident_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('incident_date', '<=', $endDate);
        }
        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Stats
        $totalIncidents = (clone $query)->count();
        $incidentsToday = (clone $query)->whereDate('incident_date', Carbon::today())->count();
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        
        // Incidents by category 
        $incidentsByCategory = \App\Models\Category::withCount(['incidents' => function($q) use ($year, $month, $startDate, $endDate, $selectedCategories) {
            if ($year) $q->whereYear('incident_date', $year);
            if ($month) $q->whereMonth('incident_date', $month);
            if ($startDate) $q->whereDate('incident_date', '>=', $startDate);
            if ($endDate) $q->whereDate('incident_date', '<=', $endDate);
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
            
        // Incidents Trend Chart — auto granularity
        $trendQuery = clone $query;

        if ($month || ($startDate && $endDate)) {
            // Specific month or explicit date range → daily
            $incidentsTrend = $trendQuery->select(
                DB::raw('DATE(incident_date) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } else {
            // No month filter — check how many distinct months exist in the result
            $monthCount = (clone $trendQuery)
                ->selectRaw("COUNT(DISTINCT TO_CHAR(incident_date, 'YYYY-MM')) as cnt")
                ->value('cnt');

            if ($monthCount <= 1) {
                // Only one month (or empty) → show daily breakdown so the chart is useful
                $incidentsTrend = $trendQuery->select(
                    DB::raw('DATE(incident_date) as date'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            } else {
                // Multiple months → monthly aggregation
                $incidentsTrend = $trendQuery->select(
                    DB::raw("TO_CHAR(incident_date, 'YYYY-MM') as date"),
                    DB::raw('count(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            }
        }

        // Crime Clock (Incidents by Hour)
        $hourlyCounts = (clone $query)->select(
            DB::raw('CAST(EXTRACT(HOUR FROM incident_date) AS INTEGER) as hour'),
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
        $availableYears = Incident::selectRaw('EXTRACT(YEAR FROM incident_date) as year')
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
