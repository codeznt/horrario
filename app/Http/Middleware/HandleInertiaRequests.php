<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'translations' => $this->getTranslations(),
            'locale' => app()->getLocale(),
            'fallbackLocale' => config('app.fallback_locale'),
        ];
    }

    /**
     * Get all translations for the current locale
     */
    private function getTranslations(): array
    {
        $locale = app()->getLocale();
        $langPath = lang_path($locale);

        if (! is_dir($langPath)) {
            return [];
        }

        $translations = [];
        $files = glob($langPath.'/*.php');

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $translations[$key] = include $file;
        }

        return $translations;
    }
}
