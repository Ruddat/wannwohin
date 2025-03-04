<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WwdeContinent;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Route;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for all continents, countries, and locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

        // Kontinente hinzufügen
        WwdeContinent::all()->each(function ($continent) use (&$sitemap) {
            $url = route('continent.countries', $continent->alias);
            $sitemap .= '    <url>' . PHP_EOL;
            $sitemap .= '        <loc>' . $url . '</loc>' . PHP_EOL;
            $sitemap .= '        <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '        <priority>0.7</priority>' . PHP_EOL;
            $sitemap .= '    </url>' . PHP_EOL;
        });

        // Länder hinzufügen
        WwdeCountry::where('status', 'active')->get()->each(function ($country) use (&$sitemap) {
            $url = route('list-country-locations', [$country->continent->alias, $country->alias]);
            $sitemap .= '    <url>' . PHP_EOL;
            $sitemap .= '        <loc>' . $url . '</loc>' . PHP_EOL;
            $sitemap .= '        <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $sitemap .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
            $sitemap .= '        <priority>0.8</priority>' . PHP_EOL;
            $sitemap .= '    </url>' . PHP_EOL;
        });

        // Standorte hinzufügen
        WwdeLocation::where('status', 'active')
                    ->where('finished', '1')
                    ->get()
                    ->each(function ($location) use (&$sitemap) {
                        $url = route('location.details', [$location->continent->alias, $location->country->alias, $location->alias]);
                        $sitemap .= '    <url>' . PHP_EOL;
                        $sitemap .= '        <loc>' . $url . '</loc>' . PHP_EOL;
                        $sitemap .= '        <lastmod>' . $location->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
                        $sitemap .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
                        $sitemap .= '        <priority>0.9</priority>' . PHP_EOL;
                        $sitemap .= '    </url>' . PHP_EOL;
                    });

        $sitemap .= '</urlset>';

        // Speichere die Sitemap
        file_put_contents(public_path('sitemap.xml'), $sitemap);

        $this->info('Sitemap generiert unter public/sitemap.xml');
    }
}
