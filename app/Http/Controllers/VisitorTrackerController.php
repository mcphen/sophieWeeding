<?php

namespace App\Http\Controllers;

use App\Models\VisitorTracker;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Setting;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class VisitorTrackerController extends Controller
{
    /**
     * Track a visitor action
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackAction(Request $request)
    {
        $validated = $request->validate([
            'page_visited' => 'required|string',
            'action' => 'nullable|string',
            'action_details' => 'nullable|string',
        ]);

        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');
        $userId = Auth::id();

        // Get location data from IP address
        $locationData = $this->getLocationFromIp($ipAddress);

        // Check if the same IP has visited the same page today
        $today = Carbon::today();
        $existingTracker = VisitorTracker::where('ip_address', $ipAddress)
            ->where('page_visited', $validated['page_visited'])
            ->whereDate('created_at', $today)
            ->first();

        // Create tracking record only if no existing record found for today
        if (!$existingTracker) {
            $tracker = VisitorTracker::create([
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'country' => $locationData['country'] ?? null,
                'city' => $locationData['city'] ?? null,
                'region' => $locationData['region'] ?? null,
                'page_visited' => $validated['page_visited'],
                'action' => $validated['action'] ?? null,
                'action_details' => $validated['action_details'] ?? null,
                'user_id' => $userId,
            ]);
        } else {
            $tracker = $existingTracker;
        }

        return response()->json(['success' => true, 'tracker_id' => $tracker->id]);
    }

    /**
     * Get location information from IP address
     *
     * @param string $ipAddress
     * @return array
     */
    private function getLocationFromIp(string $ipAddress): array
    {
        try {
            // Using ipinfo.io service - you may need to register for an API key
            // for production use or use another service
            $response = Http::get("https://ipinfo.io/{$ipAddress}/json");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'country' => $data['country'] ?? null,
                    'city' => $data['city'] ?? null,
                    'region' => $data['region'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the tracking
            \Log::error('Error getting location data: ' . $e->getMessage());
        }

        return [
            'country' => null,
            'city' => null,
            'region' => null,
        ];
    }

    /**
     * Display the visitor tracking dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        // Get visitor statistics
        $totalVisitors = VisitorTracker::distinct('ip_address')->count('ip_address');
        $totalPageViews = VisitorTracker::count();
        $today = Carbon::today();
        $uniqueVisitorsToday = VisitorTracker::whereDate('created_at', $today)
            ->distinct('ip_address')
            ->count('ip_address');
        $pageViewsToday = VisitorTracker::whereDate('created_at', $today)->count();
        $averagePageViewsPerVisitor = $totalVisitors > 0
            ? round($totalPageViews / $totalVisitors, 1)
            : 0;

        // Get top pages visited
        $topPages = VisitorTracker::select('page_visited', DB::raw('count(*) as total'))
            ->groupBy('page_visited')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $pageVisits = [
            'labels' => $topPages->pluck('page_visited')->toArray(),
            'data' => $topPages->pluck('total')->toArray(),
        ];

        // Get geographical data
        $countries = VisitorTracker::select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $cities = VisitorTracker::select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $geoData = [
            'countries' => [
                'labels' => $countries->pluck('country')->toArray(),
                'data' => $countries->pluck('total')->toArray(),
            ],
            'cities' => [
                'labels' => $cities->pluck('city')->toArray(),
                'data' => $cities->pluck('total')->toArray(),
            ],
        ];

        // Get action data
        $actions = VisitorTracker::select('action', DB::raw('count(*) as total'))
            ->whereNotNull('action')
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $actionData = [
            'labels' => $actions->pluck('action')->toArray(),
            'data' => $actions->pluck('total')->toArray(),
        ];

        // Get time-based data
        $startDate = Carbon::now()->subDays(30);
        $dailyVisitors = VisitorTracker::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors'),
                DB::raw('COUNT(*) as page_views')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $uniqueVisitors = [];
        $pageViews = [];

        foreach ($dailyVisitors as $day) {
            $dates[] = Carbon::parse($day->date)->format('M d');
            $uniqueVisitors[] = $day->unique_visitors;
            $pageViews[] = $day->page_views;
        }

        // Get hourly distribution
        $hourlyDistribution = VisitorTracker::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[] = $hourlyDistribution->get($i)->total ?? 0;
        }

        $timeData = [
            'daily' => [
                'labels' => $dates,
                'uniqueVisitors' => $uniqueVisitors,
                'pageViews' => $pageViews,
            ],
            'hourly' => [
                'labels' => range(0, 23),
                'data' => $hourlyData,
            ],
        ];

        // Get recent appointments
        $recentAppointments = Appointment::with(['client', 'services'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent contacts
        $recentContacts = Contact::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get the site logo from settings
        $siteLogo = StorageHelper::url(Setting::get('site_logo', 'images/logo.png'));

        // Return the dashboard view with data
        return Inertia::render('Dashboard', [
            'visitorStats' => [
                'totalVisitors' => $totalVisitors,
                'totalPageViews' => $totalPageViews,
                'uniqueVisitorsToday' => $uniqueVisitorsToday,
                'pageViewsToday' => $pageViewsToday,
                'averagePageViewsPerVisitor' => $averagePageViewsPerVisitor,
            ],
            'pageVisits' => $pageVisits,
            'geoData' => $geoData,
            'actionData' => $actionData,
            'timeData' => $timeData,
            'recentAppointments' => $recentAppointments,
            'recentContacts' => $recentContacts,
            'logoUrl' => $siteLogo,
        ]);
    }
}
