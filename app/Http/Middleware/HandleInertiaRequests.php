<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy(null, null, null, [
                    'only' => [
                        'home', 'about', 'services', 'products', 'product.show', 'portfolio',
                        'api.team-members.listes', 'blog', 'blog.show', 'contact', 'contact.store',
                        'appointment.create', 'appointment.store', 'appointment.confirmation',
                        'dashboard', 'login', 'password.request', 'password.email', 'password.reset',
                        'password.store', 'verification.notice', 'verification.verify',
                        'verification.send', 'password.confirm', 'logout', 'profile.edit',
                        'profile.update', 'profile.destroy', 'password.edit', 'password.update',
                        'appearance', 'api.track-action'
                        // 'register' is intentionally excluded
                    ]
                ]))->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
