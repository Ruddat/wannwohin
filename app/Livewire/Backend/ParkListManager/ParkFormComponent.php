<?php

namespace App\Livewire\Backend\ParkListManager;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\WwdeLocation;
use Livewire\WithFileUploads;
use App\Models\AmusementParks;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

class ParkFormComponent extends Component
{
    use WithFileUploads;

    public $parkId;
    public $name, $country, $location, $latitude, $longitude, $open_from, $closed_from, $url, $description, $videoUrl, $logoUrl, $embedCode;
    public $logoFile;
    public $opening_hours = [
        'monday' => ['open' => '', 'close' => ''],
        'tuesday' => ['open' => '', 'close' => ''],
        'wednesday' => ['open' => '', 'close' => ''],
        'thursday' => ['open' => '', 'close' => ''],
        'friday' => ['open' => '', 'close' => ''],
        'saturday' => ['open' => '', 'close' => ''],
        'sunday' => ['open' => '', 'close' => ''],
    ];
    public $applyToAll = false;
    public $defaultOpen = '';
    public $defaultClose = '';
    public $hasVideo = false;

    protected $rules = [
        'name' => 'required|string',
        'country' => 'required|string',
        'location' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'open_from' => 'nullable|date',
        'type' => 'nullable|string|max:100',
        'closed_from' => 'nullable|date|after:open_from',
        'url' => 'nullable|url|unique:amusement_parks,url', // Eindeutigkeit prüfen
        'description' => 'nullable|string|max:500',
        'videoUrl' => 'nullable|url',
        'embedCode' => 'nullable|string',
        'logoUrl' => 'nullable|string',
        'logoFile' => 'nullable|image|mimes:jpg,png,svg,webp|max:2048|dimensions:min_width=50,min_height=50',
        'defaultOpen' => 'nullable|date_format:H:i',
        'defaultClose' => 'nullable|date_format:H:i|after:defaultOpen',
        'opening_hours.monday.open' => 'nullable|date_format:H:i',
        'opening_hours.monday.close' => 'nullable|date_format:H:i|after:opening_hours.monday.open',
        'opening_hours.tuesday.open' => 'nullable|date_format:H:i',
        'opening_hours.tuesday.close' => 'nullable|date_format:H:i|after:opening_hours.tuesday.open',
        'opening_hours.wednesday.open' => 'nullable|date_format:H:i',
        'opening_hours.wednesday.close' => 'nullable|date_format:H:i|after:opening_hours.wednesday.open',
        'opening_hours.thursday.open' => 'nullable|date_format:H:i',
        'opening_hours.thursday.close' => 'nullable|date_format:H:i|after:opening_hours.thursday.open',
        'opening_hours.friday.open' => 'nullable|date_format:H:i',
        'opening_hours.friday.close' => 'nullable|date_format:H:i|after:opening_hours.friday.open',
        'opening_hours.saturday.open' => 'nullable|date_format:H:i',
        'opening_hours.saturday.close' => 'nullable|date_format:H:i|after:opening_hours.saturday.open',
        'opening_hours.sunday.open' => 'nullable|date_format:H:i',
        'opening_hours.sunday.close' => 'nullable|date_format:H:i|after:opening_hours.sunday.open',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $park = AmusementParks::find($id);
            if ($park) {
                $this->fill([
                    'parkId' => $park->id,
                    'name' => $park->name,
                    'type' => $park->type,
                    'country' => $park->country,
                    'location' => $park->location,
                    'latitude' => $park->latitude,
                    'longitude' => $park->longitude,
                    'open_from' => $park->open_from,
                    'closed_from' => $park->closed_from,
                    'url' => $park->url,
                    'description' => $park->description,
                    'videoUrl' => $park->video_url,
                    'embedCode' => $park->embed_code,
                    'logoUrl' => $park->logo_url,
                ]);
                $this->hasVideo = !empty($park->video_url) || !empty($park->embed_code);
                if ($park->opening_hours && $park->opening_hours !== 'null') {
                    $decoded = json_decode($park->opening_hours, true);
                    if (is_array($decoded)) {
                        $this->opening_hours = array_merge($this->opening_hours, $decoded);
                        $firstDay = reset($decoded);
                        $allSame = true;
                        foreach ($decoded as $day) {
                            if ($day['open'] !== $firstDay['open'] || $day['close'] !== $firstDay['close']) {
                                $allSame = false;
                                break;
                            }
                        }
                        if ($allSame && $firstDay['open'] && $firstDay['close']) {
                            $this->applyToAll = true;
                            $this->defaultOpen = $firstDay['open'];
                            $this->defaultClose = $firstDay['close'];
                        }
                    }
                }
            }
        }
    }

    public function updatedApplyToAll($value)
    {
        if ($value && $this->defaultOpen && $this->defaultClose) {
            foreach ($this->opening_hours as &$day) {
                $day['open'] = $this->defaultOpen;
                $day['close'] = $this->defaultClose;
            }
        }
    }

    public function updatedLogoFile()
    {
        try {
            $this->validateOnly('logoFile');
            $parkName = $this->name ?: 'unnamed_park';
            $fileName = 'logo_' . Str::slug($parkName) . '_' . time() . '.' . $this->logoFile->extension();
            $directory = public_path('img/parklogos');
            File::ensureDirectoryExists($directory);
            $this->logoFile->storeAs('', $fileName, 'public_parklogos');
            $this->logoUrl = '/img/parklogos/' . $fileName;
            $this->dispatch('show-toast', type: 'success', message: 'Logo erfolgreich hochgeladen.');
        } catch (\Exception $e) {
            Log::error('Fehler beim Hochladen des Logos:', ['error' => $e->getMessage()]);
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Hochladen des Logos: ' . $e->getMessage());
        }
    }

    public function updateCoordinates()
    {
        if (!$this->name) {
            $this->dispatch('show-toast', type: 'error', message: 'Parkname fehlt.');
            return;
        }

        try {
            $geocodeService = app(\App\Services\GeocodeService::class);
            $results = $geocodeService->searchByParkName($this->name);

            if (empty($results) || !isset($results[0]['lat']) || !isset($results[0]['lon'])) {
                throw new \Exception('Keine Koordinaten für "' . $this->name . '" gefunden.');
            }

            $result = $results[0];
            $this->latitude = (float) $result['lat'];
            $this->longitude = (float) $result['lon'];

            $address = $result['address'] ?? [];
            $this->country = $address['country'] ?? $this->country;
            $this->location = $address['town'] ?? $address['city'] ?? $address['village'] ?? $this->location;

            $park = $this->parkId ? AmusementParks::find($this->parkId) : null;
            if ($park) {
                $park->latitude = $this->latitude;
                $park->longitude = $this->longitude;
                $park->country = $this->country;
                $park->location = $this->location;
                $park->save();
            }

            $this->dispatch('show-toast', type: 'success', message: 'Koordinaten und Adressdaten wurden erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Fehler: ' . $e->getMessage());
        }
    }

    public function save()
    {
        // Dynamische Regeln basierend auf Park-ID
        $rules = $this->rules;
        if ($this->parkId) {
            $rules['url'] = 'nullable|url|unique:amusement_parks,url,' . $this->parkId;
        } else {
            $rules['url'] = 'nullable|url|unique:amusement_parks,url';
        }

        $this->validate($rules);

        $externalId = Str::slug($this->name, '');

        $openingHours = is_array($this->opening_hours) ? $this->opening_hours : [];
        if ($this->applyToAll && $this->defaultOpen && $this->defaultClose) {
            foreach ($openingHours as &$day) {
                $day['open'] = $this->defaultOpen;
                $day['close'] = $this->defaultClose;
            }
        }

        $hasOpeningHours = false;
        foreach ($openingHours as $day) {
            if (!empty($day['open']) || !empty($day['close'])) {
                $hasOpeningHours = true;
                break;
            }
        }

        $data = [
            'name' => $this->name,
            'country' => $this->country,
            'type' => $this->type,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'open_from' => $this->open_from,
            'closed_from' => $this->closed_from,
            'url' => $this->url,
            'description' => $this->description,
            'video_url' => $this->videoUrl,
            'embed_code' => $this->embedCode,
            'logo_url' => $this->logoUrl,
            'opening_hours' => $hasOpeningHours ? json_encode($openingHours) : null,
            'external_id' => $externalId,
        ];

        $today = date('Y-m-d');

        if ($this->parkId) {
            $park = AmusementParks::findOrFail($this->parkId);
            $park->update($data);
            Cache::forget("queue_times_park_{$this->parkId}_" . date('H'));
            $this->clearAmusementParksCache($park, $today);
            Log::info('Cache gelöscht', ['keys' => ["queue_times_park_{$this->parkId}_" . date('H')]]);
            $this->dispatch('show-toast', type: 'success', message: 'Park erfolgreich aktualisiert.');
            $this->dispatch('close-modal');
        } else {
            $newPark = AmusementParks::create($data);
            if ($newPark->queue_times_id) {
                Cache::forget("queue_times_park_{$newPark->id}_" . date('H'));
            }
            $this->clearAmusementParksCache($newPark, $today);
            Log::info('Cache gelöscht', ['keys' => $newPark->queue_times_id ? ["queue_times_park_{$newPark->id}_" . date('H')] : []]);
            $this->dispatch('show-toast', type: 'success', message: 'Park erfolgreich erstellt.');
            return redirect()->route('verwaltung.site-manager.park-manager.index');
        }
    }

    /**
     * Hilfsmethode zum Löschen des amusement_parks-Caches für betroffene Standorte
     */
    private function clearAmusementParksCache($park, string $date)
    {
        // Alle Standorte abrufen, die den Park in einem Radius von 150 km enthalten könnten
        $locations = WwdeLocation::selectRaw("id, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance", [$park->latitude, $park->longitude, $park->latitude])
            ->having('distance', '<=', 150) // Standardradius aus fetchAmusementParks
            ->get();

        foreach ($locations as $location) {
            // Lösche den Cache für jeden Standort und den Standardradius (150)
            $cacheKey = "amusement_parks_{$location->id}_radius_150_{$date}";
            Cache::forget($cacheKey);
            Log::info('Cache gelöscht', ['key' => $cacheKey]);
        }
    }

    public function scrapeData()
    {
        $this->validate([
            'url' => 'required|url',
        ]);

        try {
            $response = Http::get($this->url);
            $html = $response->body();
            $crawler = new Crawler($html);

            $this->name = $crawler->filter('title')->count() ? trim(explode(' I ', $crawler->filter('title')->text())[0]) : 'Unbekannt';
            $this->description = $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : '';
            $this->location = $crawler->filter('.address')->count() ? $crawler->filter('.address')->text() : '';
            $this->country = $crawler->filter('html')->count() ? strtoupper($crawler->filter('html')->attr('lang')) : 'DE';

            $hours = $crawler->filter('.opening-hours')->count() ? $crawler->filter('.opening-hours')->text() : '';
            if ($hours) {
                $this->defaultOpen = '09:00';
                $this->defaultClose = '18:00';
                $this->applyToAll = true;
            }

            $this->hasVideo = false;
            $this->videoUrl = null;
            $this->embedCode = null;

            $videoElement = $crawler->filter('video[src]');
            if ($videoElement->count() > 0) {
                $this->hasVideo = true;
                $this->videoUrl = $videoElement->attr('src');
                $this->videoUrl = filter_var($this->videoUrl, FILTER_VALIDATE_URL) ? $this->videoUrl : rtrim($this->url, '/') . '/' . ltrim($this->videoUrl, '/');
                Log::info('Video gefunden in <video src>:', ['videoUrl' => $this->videoUrl]);
            }

            if (!$this->hasVideo) {
                $sourceElement = $crawler->filter('video source[src]');
                if ($sourceElement->count() > 0) {
                    $this->hasVideo = true;
                    $this->videoUrl = $sourceElement->attr('src');
                    $this->videoUrl = filter_var($this->videoUrl, FILTER_VALIDATE_URL) ? $this->videoUrl : rtrim($this->url, '/') . '/' . ltrim($this->videoUrl, '/');
                    Log::info('Video gefunden in <source src>:', ['videoUrl' => $this->videoUrl]);
                }
            }

            if (!$this->hasVideo) {
                $iframeElement = $crawler->filter('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]');
                if ($iframeElement->count() > 0) {
                    $this->hasVideo = true;
                    $this->embedCode = $crawler->filter('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]')->outerHtml();
                    $this->videoUrl = $iframeElement->attr('src');
                    Log::info('Video gefunden in <iframe>:', ['embedCode' => $this->embedCode, 'videoUrl' => $this->videoUrl]);
                }
            }

            if (!$this->hasVideo) {
                $links = $crawler->filter('a[href*="youtube.com/watch"], a[href*="vimeo.com/"]');
                if ($links->count() > 0) {
                    $this->hasVideo = true;
                    $this->videoUrl = $links->first()->attr('href');
                    if (str_contains($this->videoUrl, 'youtube.com/watch')) {
                        $this->videoUrl = str_replace('watch?v=', 'embed/', $this->videoUrl);
                        $this->embedCode = '<iframe width="560" height="315" src="' . $this->videoUrl . '" frameborder="0" allowfullscreen></iframe>';
                    }
                    Log::info('Video gefunden in <a href>:', ['embedCode' => $this->embedCode, 'videoUrl' => $this->videoUrl]);
                }
            }

            if (!$this->hasVideo) {
                $bodyText = $crawler->filter('body')->text();
                if (preg_match('/(https?:\/\/(?:www\.)?(youtube\.com\/watch\?v=|vimeo\.com\/)[^\s]+)/', $bodyText, $match)) {
                    $this->hasVideo = true;
                    $this->videoUrl = $match[0];
                    if (str_contains($this->videoUrl, 'youtube.com/watch')) {
                        $this->videoUrl = str_replace('watch?v=', 'embed/', $this->videoUrl);
                        $this->embedCode = '<iframe width="560" height="315" src="' . $this->videoUrl . '" frameborder="0" allowfullscreen></iframe>';
                    }
                    Log::info('Video gefunden im Text:', ['embedCode' => $this->embedCode, 'videoUrl' => $this->videoUrl]);
                }
            }

            if (!$this->hasVideo) {
                Log::info('Kein Video gefunden auf der Seite:', ['url' => $this->url]);
            } else {
                Log::info('Video-Details:', ['hasVideo' => $this->hasVideo, 'embedCode' => $this->embedCode, 'videoUrl' => $this->videoUrl]);
            }

            $logoElement = $crawler->filter('.cf-header__logo img, .cf-header__logo--small img, img.logo, img[alt*="logo"], header img');
            if ($logoElement->count() > 0) {
                $logoUrl = $logoElement->first()->attr('src');
                $logoUrl = filter_var($logoUrl, FILTER_VALIDATE_URL) ? $logoUrl : rtrim($this->url, '/') . '/' . ltrim($logoUrl, '/');
                $response = Http::timeout(10)->get($logoUrl);
                if ($response->successful() && str_contains($response->header('Content-Type'), 'image')) {
                    $extension = pathinfo($logoUrl, PATHINFO_EXTENSION) ?: 'jpg';
                    $fileName = 'logo_' . Str::slug($this->name) . '_' . time() . '.' . $extension;
                    $filePath = public_path('img/parklogos') . '/' . $fileName;
                    File::ensureDirectoryExists(public_path('img/parklogos'));
                    file_put_contents($filePath, $response->body());
                    $this->logoUrl = '/img/parklogos/' . $fileName;
                }
            }

            $this->dispatch('show-toast', type: 'success', message: 'Daten erfolgreich gescraped!');
        } catch (\Exception $e) {
            Log::error('Fehler beim Scrapen:', ['error' => $e->getMessage()]);
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Scrapen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.backend.park-list-manager.park-form-component')
        ->layout('raadmin.layout.master');
    }
}
