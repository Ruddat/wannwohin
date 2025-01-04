<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class GenerateBreadcrumbs
{
    public function handle($request, Closure $next)
    {
        $breadcrumbs = [];

        // Füge die Startseite hinzu
        $breadcrumbs[] = [
            'title' => 'Home',
            'url' => route('home'),
        ];

        // Hole die aktuelle Route
        $currentRoute = Route::current();
        if (!$currentRoute) {
            View::share('breadcrumbs', $breadcrumbs);
            return $next($request);
        }

        $routeName = $currentRoute->getName();
        $routeParameters = $currentRoute->parameters();

        // Generiere Breadcrumbs basierend auf benannten Routen
        switch ($routeName) {
            case 'continent.countries':
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['continentAlias']),
                    'url' => route('continent.countries', ['continentAlias' => $routeParameters['continentAlias']]),
                ];
                break;

            case 'list-country-locations':
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['continentAlias']),
                    'url' => route('continent.countries', ['continentAlias' => $routeParameters['continentAlias']]),
                ];
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['countryAlias']),
                    'url' => route('list-country-locations', [
                        'continentAlias' => $routeParameters['continentAlias'],
                        'countryAlias' => $routeParameters['countryAlias'],
                    ]),
                ];
                break;

            case 'location.details':
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['continent']),
                    'url' => route('continent.countries', ['continentAlias' => $routeParameters['continent']]),
                ];
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['country']),
                    'url' => route('list-country-locations', [
                        'continentAlias' => $routeParameters['continent'],
                        'countryAlias' => $routeParameters['country'],
                    ]),
                ];
                $breadcrumbs[] = [
                    'title' => ucfirst($routeParameters['location']),
                    'url' => route('location.details', [
                        'continent' => $routeParameters['continent'],
                        'country' => $routeParameters['country'],
                        'location' => $routeParameters['location'],
                    ]),
                ];
                break;

            default:
                break;
        }

        // Teile die Breadcrumbs mit allen Views
        View::share('breadcrumbs', $breadcrumbs);

        return $next($request);
    }
}
