<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use App\Helpers\StorageHelper;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('appearance', $request->cookie('appearance') ?? 'system');

        // Share the site logo with the Blade template
        $siteLogo = StorageHelper::url(Setting::get('site_logo', 'images/logo.png'));
        View::share('siteLogo', $siteLogo);

        return $next($request);
    }
}
