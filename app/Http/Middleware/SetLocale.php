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
        $configLocale = config("app.locale", "ar");
        $availableLocales = config("app.available_locales", ["en", "ar"]);
        
        // إذا لم يكن هناك locale في الجلسة، قم بتعيين العربية كافتراضية
        if (!$sessionLocale) {
            $locale = $configLocale ?: "ar";
            // تأكد من أن اللغة العربية هي الافتراضية
            if (!in_array($locale, $availableLocales)) {
                $locale = "ar";
            }
            // حفظ اللغة في الجلسة لضمان الاستمرارية
            Session::put("locale", $locale);
        } else {
            $locale = $sessionLocale;
        }
        
        // التحقق من صحة اللغة
        if (!in_array($locale, $availableLocales)) {
            $locale = "ar";
            Session::put("locale", $locale);
        }
        
        Log::info('SetLocale middleware', [
            'session_locale' => $sessionLocale,
            'config_locale' => $configLocale,
            'final_locale' => $locale,
            'available_locales' => $availableLocales
        ]);
        
        App::setLocale($locale);
        Log::info('Locale set successfully', ['locale' => $locale]);
        
        return $next($request);
    }
}
