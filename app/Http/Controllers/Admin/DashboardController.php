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
        ->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->incidents_count,
                'color' => $category->color
            ];
        });
            
        // Incidents Trend Chart
        $trendQuery = clone $query;
        
        if ($year && !$month && !$startDate && !$endDate) {
            // Group by month
            $incidentsTrend = $trendQuery->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as date"), 
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } else {
            // Group by day
            if (!$year && !$month && !$startDate && !$endDate) {
                // Default: last 30 days
                $trendQuery->where('created_at', '>=', Carbon::now()->subDays(30));
            }
            
            $incidentsTrend = $trendQuery->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        }

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
