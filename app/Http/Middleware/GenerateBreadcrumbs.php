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

        // "Home" als erster Eintrag (immer klickbar)
        $breadcrumbs[] = [
            'title' => 'Home',
            'url' => route('home'),
        ];

        // Aktuelle Route abrufen
        $currentRoute = Route::current();
        if (!$currentRoute) {
            View::share('breadcrumbs', $breadcrumbs);
            return $next($request);
        }

        $routeName = $currentRoute->getName();
        $routeParameters = $currentRoute->parameters();

        // Dynamische Breadcrumbs für verschiedene Routen
        switch ($routeName) {
            case 'explore':
                $breadcrumbs[] = [
                    'title' => 'Abenteuer Finden', // Title for the /explore page
                    'url' => route('explore'),
                ];
                break;

            case 'explore.results':
                $breadcrumbs[] = [
                    'title' => 'Abenteuer Finden',
                    'url' => route('explore'),
                ];
                $breadcrumbs[] = [
                    'title' => 'Ergebnisse', // Title for the /explore/results page
                    'url' => route('explore.results'),
                ];
                break;

            case 'search.results':
                $breadcrumbs[] = [
                    'title' => 'Suchergebnisse',
                    'url' => route('search.results'),
                ];
                break;

            case 'detail_search':
                $breadcrumbs[] = [
                    'title' => 'Detailsuche',
                    'url' => route('detail_search'),
                ];
                break;

            case 'detail_search_result':
                $breadcrumbs[] = [
                    'title' => 'Detailsuche',
                    'url' => route('detail_search'),
                ];
                $breadcrumbs[] = [
                    'title' => 'Ergebnisse',
                    'url' => route('detail_search_result'),
                ];
                break;

            case 'ergebnisse.anzeigen':
                $breadcrumbs[] = [
                    'title' => 'Alle Ergebnisse',
                    'url' => route('ergebnisse.anzeigen'),
                ];
                break;

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

            case 'impressum':
                $breadcrumbs[] = [
                    'title' => 'Impressum',
                    'url' => route('impressum'),
                ];
                break;

            default:
                break;
        }

        // Breadcrumbs für Views verfügbar machen
        View::share('breadcrumbs', $breadcrumbs);

        return $next($request);
    }
}
