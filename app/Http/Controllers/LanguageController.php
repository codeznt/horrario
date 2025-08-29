<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application locale
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Validate that the locale is supported
        $supportedLocales = ['en', 'uk'];

        if (! in_array($locale, $supportedLocales)) {
            return back();
        }

        // Store the selected locale in session
        session(['locale' => $locale]);

        // Set the application locale for the current request
        app()->setLocale($locale);

        return back();
    }
}
