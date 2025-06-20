<?php

namespace App\Http\Middleware;

use App\Models\VisitorTracker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for certain routes or requests
        if ($this->shouldSkipTracking($request)) {
            return $next($request);
        }

        // Process the request first
        $response = $next($request);

        // Track the visit after the request is processed
        // This is done in a non-blocking way to not affect performance
        $this->trackVisit($request);

        return $response;
    }

    /**
     * Determine if tracking should be skipped for this request
     *
     * @param Request $request
     * @return bool
     */
    private function shouldSkipTracking(Request $request): bool
    {
        // Skip API requests except for the tracking endpoint itself
        if ($request->is('api/*') && !$request->is('api/track-action')) {
            return true;
        }

        // Skip asset requests
        if ($request->is('_debugbar/*', 'js/*', 'css/*', 'images/*', 'fonts/*')) {
            return true;
        }

        // Skip API-like routes that don't have the api/ prefix
        $path = $request->path();
        if (
            // Routes ending with /listes are typically API endpoints
            str_ends_with($path, '/listes') ||
            // Routes with .json extension
            str_ends_with($path, '.json') ||
            // Routes that are likely to be API endpoints based on naming conventions
            preg_match('/(\/data$|\/export$|\/import$|\/search$|\/autocomplete$)/', $path)
        ) {
            return true;
        }

        // Skip tracking for bots/crawlers
        $userAgent = $request->header('User-Agent');
        if ($this->isBot($userAgent)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user agent is a bot or crawler
     *
     * @param string|null $userAgent
     * @return bool
     */
    private function isBot(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        $botKeywords = [
            'bot', 'spider', 'crawler', 'scraper', 'yahoo', 'slurp', 'daum', 'wget', 'curl',
            'lighthouse', 'pingdom', 'googlebot', 'bingbot', 'yandex', 'baiduspider'
        ];

        foreach ($botKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Track the visit
     *
     * @param Request $request
     * @return void
     */
    private function trackVisit(Request $request): void
    {
        try {
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $userId = Auth::id();
            $pageVisited = $request->path() === '/' ? 'home' : $request->path();

            // Get location data from IP address
            $locationData = $this->getLocationFromIp($ipAddress);

            // Check if the same IP has visited the same page today
            $today = Carbon::today();
            $existingTracker = VisitorTracker::where('ip_address', $ipAddress)
                ->where('page_visited', $pageVisited)
                ->whereDate('created_at', $today)
                ->first();

            // Create tracking record only if no existing record found for today
            if (!$existingTracker) {
                VisitorTracker::create([
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'country' => $locationData['country'] ?? null,
                    'city' => $locationData['city'] ?? null,
                    'region' => $locationData['region'] ?? null,
                    'page_visited' => $pageVisited,
                    'action' => 'page_view',
                    'user_id' => $userId,
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Error tracking visitor: ' . $e->getMessage());
        }
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
}
