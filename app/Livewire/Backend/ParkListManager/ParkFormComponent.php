<?php

namespace App\Livewire\Backend\ParkListManager;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\AmusementParks;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ParkFormComponent extends Component
{
    public $parkId;
    public $name, $country, $location, $latitude, $longitude, $open_from, $closed_from, $url, $description, $videoUrl;
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
        'closed_from' => 'nullable|date|after:open_from',
        'url' => 'nullable|url',
        'description' => 'nullable|string|max:500',
        'videoUrl' => 'nullable|url',
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
                    'name' => $park->name,
                    'country' => $park->country,
                    'location' => $park->location,
                    'latitude' => $park->latitude,
                    'longitude' => $park->longitude,
                    'open_from' => $park->open_from,
                    'closed_from' => $park->closed_from,
                    'url' => $park->url,
                    'description' => $park->description,
                    'videoUrl' => $park->video_url,
                ]);
                $this->parkId = $park->id;
                $this->hasVideo = !empty($park->video_url);

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

    public function save()
    {
        $this->validate();

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
            if (isset($day['open']) && !empty($day['open']) || isset($day['close']) && !empty($day['close'])) {
                $hasOpeningHours = true;
                break;
            }
        }

        $data = [
            'name' => $this->name,
            'country' => $this->country,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'open_from' => $this->open_from,
            'closed_from' => $this->closed_from,
            'url' => $this->url,
            'description' => $this->description,
            'video_url' => $this->videoUrl,
            'opening_hours' => $hasOpeningHours ? json_encode($openingHours) : null,
            'external_id' => $externalId,
        ];

        if ($this->parkId) {
            $park = AmusementParks::findOrFail($this->parkId);
            $park->update($data);
            $this->dispatch('show-toast', type: 'success', message: 'Park erfolgreich aktualisiert.');
        } else {
            AmusementParks::create($data);
            $this->dispatch('show-toast', type: 'success', message: 'Park erfolgreich erstellt.');
        }

        return redirect()->route('verwaltung.site-manager.park-manager.index');
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

            // Name aus <title>
            $this->name = $crawler->filter('title')->count() ? trim(explode(' I ', $crawler->filter('title')->text())[0]) : 'Unbekannt';
            Log::info('Gescrapter Name:', ['name' => $this->name]);

            // Description aus <meta>
            $this->description = $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : '';
            Log::info('Gescrapte Beschreibung:', ['description' => $this->description]);

            // Location (anpassen an echte Struktur)
            $this->location = $crawler->filter('.address')->count() ? $crawler->filter('.address')->text() : '';
            Log::info('Gescrapter Standort:', ['location' => $this->location]);

            // Land (z. B. aus HTML lang-Attribut)
            $this->country = $crawler->filter('html')->count() ? strtoupper($crawler->filter('html')->attr('lang')) : 'DE';
            Log::info('Gescraptes Land:', ['country' => $this->country]);

            // Öffnungszeiten (Platzhalter, anpassen an echte Seite)
            $hours = $crawler->filter('.opening-hours')->count() ? $crawler->filter('.opening-hours')->text() : '';
            Log::info('Gescrapte Öffnungszeiten:', ['hours' => $hours]);
            if ($hours) {
                $this->defaultOpen = '09:00';
                $this->defaultClose = '18:00';
                $this->applyToAll = true;
            }

            // Video-Check und URL extrahieren
            $this->hasVideo = false;
            $this->videoUrl = null;
            $videoElement = $crawler->filter('video[src]');
            if ($videoElement->count() > 0) {
                $this->hasVideo = true;
                $this->videoUrl = $videoElement->attr('src');
                $this->videoUrl = filter_var($this->videoUrl, FILTER_VALIDATE_URL) ? $this->videoUrl : rtrim($this->url, '/') . '/' . ltrim($this->videoUrl, '/');
            } else {
                // Prüfe <source> innerhalb von <video>
                $sourceElement = $crawler->filter('video source[src]');
                if ($sourceElement->count() > 0) {
                    $this->hasVideo = true;
                    $this->videoUrl = $sourceElement->attr('src');
                    $this->videoUrl = filter_var($this->videoUrl, FILTER_VALIDATE_URL) ? $this->videoUrl : rtrim($this->url, '/') . '/' . ltrim($this->videoUrl, '/');
                } else {
                    // Prüfe YouTube/Vimeo iframes
                    $iframeElement = $crawler->filter('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]');
                    if ($iframeElement->count() > 0) {
                        $this->hasVideo = true;
                        $this->videoUrl = $iframeElement->attr('src');
                    }
                }
            }
            Log::info('Video-Details:', ['hasVideo' => $this->hasVideo, 'videoUrl' => $this->videoUrl]);

            $this->dispatch('show-toast', type: 'success', message: 'Daten erfolgreich gescraped!');
        } catch (\Exception $e) {
            Log::error('Fehler beim Scrapen:', ['error' => $e->getMessage()]);
            $this->dispatch('show-toast', type: 'error', message: 'Fehler beim Scrapen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.backend.park-list-manager.park-form-component')
            ->layout('backend.layouts.livewiere-main');
    }
}
