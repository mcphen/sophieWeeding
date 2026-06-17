<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProspectAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('prospect_email')) {
            return redirect()->route('prospect.portal.login')
                ->with('info', 'Veuillez vous connecter pour accéder à votre espace.');
        }

        return $next($request);
    }
}
