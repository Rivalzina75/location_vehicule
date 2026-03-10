<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'fr'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if locale is set in session (user manually changed language)
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, $this->supportedLocales)) {
                App::setLocale($locale);

                return $next($request);
            }
        }

        // 2. Check URL parameter (for language switcher)
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, $this->supportedLocales)) {
                Session::put('locale', $locale);
                App::setLocale($locale);

                return $next($request);
            }
        }

        // 3. Detect from browser Accept-Language header
        $browserLocale = $this->detectBrowserLocale($request);
        if ($browserLocale) {
            Session::put('locale', $browserLocale);
            App::setLocale($browserLocale);

            return $next($request);
        }

        // 4. Fallback to default locale (fr for French site)
        App::setLocale(config('app.locale', 'fr'));

        return $next($request);
    }

    /**
     * Detect the preferred locale from browser headers
     */
    protected function detectBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        // Example: "fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7"
        $languages = [];

        foreach (explode(',', $acceptLanguage) as $part) {
            $part = trim($part);
            $parts = explode(';', $part);
            $lang = strtolower(substr($parts[0], 0, 2)); // Get first 2 chars (fr, en, etc.)

            $q = 1.0;
            if (isset($parts[1]) && str_starts_with($parts[1], 'q=')) {
                $q = (float) substr($parts[1], 2);
            }

            if (in_array($lang, $this->supportedLocales)) {
                $languages[$lang] = $q;
            }
        }

        if (empty($languages)) {
            return null;
        }

        // Sort by priority (q value) descending
        arsort($languages);

        // Return the highest priority supported locale
        return array_key_first($languages);
    }
}
