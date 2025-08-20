<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function change($locale)
    {
        Log::info('Language change requested', ['locale' => $locale, 'available' => config('app.available_locales')]);
        
        if (in_array($locale, config("app.available_locales", ["en", "ar"]))) {
            Session::put("locale", $locale);
            App::setLocale($locale);
            
            Log::info('Language changed successfully', [
                'new_locale' => $locale,
                'session_locale' => Session::get('locale'),
                'app_locale' => App::getLocale()
            ]);
            
            // Redirect to login page instead of back to avoid redirect loop
            return redirect()->route('login');
        } else {
            Log::warning('Invalid locale requested', ['locale' => $locale]);
            return redirect()->route('login');
        }
    }
}
