<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionLocale = Session::get("locale");
        $configLocale = config("app.locale");
        $availableLocales = config("app.available_locales", ["en", "ar"]);
        
        $locale = $sessionLocale ?: $configLocale;
        
        Log::info('SetLocale middleware', [
            'session_locale' => $sessionLocale,
            'config_locale' => $configLocale,
            'final_locale' => $locale,
            'available_locales' => $availableLocales
        ]);
        
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Log::info('Locale set successfully', ['locale' => $locale]);
        } else {
            Log::warning('Invalid locale in middleware', ['locale' => $locale]);
        }
        
        return $next($request);
    }
}
