<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Http;

class GeocodeActivities extends Command
{
    protected $signature = 'geocode:activities';
    protected $description = 'Geocode activities in mod_location_filters table using Nominatim';

    public function handle()
    {
        $activities = ModLocationFilter::whereNull('latitude')
            ->where('is_active', 1)
            ->get();

        foreach ($activities as $activity) {
            // Kombiniere den Aktivitätsnamen und die Stadt (location_id könnte eine Stadt-ID sein, die du übersetzen musst)
            $address = $activity->uschrift . ', ' . $this->getCityName($activity->location_id);

            // Nominatim-Anfrage
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            $data = $response->json();

            if (!empty($data) && isset($data[0])) {
                $activity->latitude = $data[0]['lat'];
                $activity->longitude = $data[0]['lon'];
                $activity->save();

                $this->info("Geocoded: {$activity->uschrift} - Lat: {$data[0]['lat']}, Lng: {$data[0]['lon']}");
            } else {
                $this->error("Failed to geocode: {$activity->uschrift}");
            }

            // Nominatim Rate-Limit: 1 Anfrage pro Sekunde
            sleep(1);
        }

        $this->info('Geocoding completed!');
    }

    // Hilfsfunktion, um die Stadt basierend auf location_id zu ermitteln
    private function getCityName($locationId)
    {
        // Hier musst du die Stadt basierend auf der location_id ermitteln
        // Beispiel: Annahme, dass location_id 13 für Frankfurt steht
        $cities = [
            13 => 'Frankfurt',
            14 => 'Verona', // Beispiel für einen anderen Ort
            // Füge weitere Städte hinzu
        ];

        return $cities[$locationId] ?? 'Unknown City';
    }
}
