<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Log;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $sessionLocale = session('locale');
        
        Log::info('HandleInertiaRequests share', [
            'app_locale' => $locale,
            'session_locale' => $sessionLocale,
            'request_url' => $request->url(),
            'user_agent' => $request->userAgent()
        ]);
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getPermissionNames(),
                ] : null,
            ],
            'locale' => $locale,
            'language' => __('app.language'),
            'available_locales' => config('app.available_locales'),
            'debug_info' => [
                'session_locale' => $sessionLocale,
                'app_locale' => $locale,
                'config_locale' => config('app.locale'),
            ],
        ];
    }
}
