<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LanguageController;

Route::get("/", function () {
    return redirect()->route('login');
});

Route::get("/language/{locale}", [LanguageController::class, "change"])
    ->name("language.change")
    ->where("locale", "[a-z]{2}");

require __DIR__."/auth.php";
