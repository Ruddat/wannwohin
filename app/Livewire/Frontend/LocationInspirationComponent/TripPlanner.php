<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use App\Models\ModTrip;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TripPlanner extends Component
{
    public bool $isVisible = false;
    public bool $useDays = false;

    public string $tripName = '';
    public string $tripDescription = '';

    public array $tripDays = [];
    public array $tripActivities = [];

    public string $locationTitle = '';

    private bool $isSyncing = false;

    protected $rules = [
        'tripName' => 'required|min:3|max:100',
        'tripDescription' => 'nullable|string|max:500',
        'tripDays.*.notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'tripName.required' => 'Der Trip-Name ist erforderlich.',
        'tripName.min' => 'Der Trip-Name muss mindestens 3 Zeichen lang sein.',
        'tripName.max' => 'Der Trip-Name darf maximal 100 Zeichen lang sein.',
        'tripDescription.max' => 'Die Beschreibung darf maximal 500 Zeichen lang sein.',
        'tripDays.*.notes.max' => 'Die Notizen dürfen maximal 1000 Zeichen lang sein.',
    ];

    public function mount()
    {
        $this->loadTripFromSession();

        $this->locationTitle = session('trip_location_title', ''); // oder z. B. "Berlin"
        $this->loadTripFromSession();
    }

    #[On('open-trip-planner')]
    public function showModal()
    {
        $this->loadTripFromSession();
        $this->syncActivitiesWithFavorites();
        $this->isVisible = true;
    }

    public function hideModal()
    {
        $this->syncTripToSession();
        $this->isVisible = false;
    }

    #[On('favorites-updated')]
    public function syncActivitiesWithFavorites()
    {
        if ($this->isSyncing) return;
        $this->isSyncing = true;

        $favorites = session('favorite_activities', []);

        if ($this->useDays) {
            foreach ($this->tripDays as &$day) {
                $day['activities'] = array_values(array_filter(
                    $day['activities'],
                    fn($activity) => in_array($activity['id'], collect($favorites)->pluck('id')->toArray())
                ));

                foreach ($favorites as $favorite) {
                    if (!collect($day['activities'])->pluck('id')->contains($favorite['id'])) {
                        $day['activities'][] = $favorite;
                    }
                }
            }
            unset($day);

            $nonEmptyDays = array_values(array_filter(
                $this->tripDays,
                fn($day) => !empty($day['activities']) || !empty($day['notes'])
            ));

            $this->tripDays = !empty($nonEmptyDays) ? $nonEmptyDays : [[
                'name' => 'Tag 1',
                'notes' => '',
                'activities' => $favorites,
            ]];
        } else {
            $this->tripActivities = $favorites;
        }

        $this->syncTripToSession(false);
        $this->isSyncing = false;
    }

    private function loadTripFromSession()
    {
        $trip = session('trip_planner');

        $this->tripName = $trip['name'] ?? '';
        $this->tripDescription = $trip['description'] ?? '';
        $this->useDays = $trip['use_days'] ?? false;

        if ($this->useDays) {
            $this->tripDays = $trip['days'] ?? [[
                'name' => 'Tag 1',
                'notes' => '',
                'activities' => session('favorite_activities', []),
            ]];
        } else {
            $this->tripActivities = $trip['activities'] ?? session('favorite_activities', []);
        }
    }

    public function addTripDay()
    {
        $this->tripDays[] = [
            'name' => 'Tag ' . (count($this->tripDays) + 1),
            'notes' => '',
            'activities' => [],
        ];
        $this->syncTripToSession();
    }

    public function removeTripDay($index)
    {
        if (isset($this->tripDays[$index]) && count($this->tripDays) > 1) {
            unset($this->tripDays[$index]);
            $this->tripDays = array_values($this->tripDays);
            $this->syncTripToSession();
        }
    }

    public function updateDayNotes($index, $notes)
    {
        if (isset($this->tripDays[$index])) {
            $this->tripDays[$index]['notes'] = $notes;
            $this->validateOnly("tripDays.{$index}.notes");
            $this->syncTripToSession();
        }
    }

    public function removeFromTrip($id)
    {
        foreach ($this->tripDays as &$day) {
            $day['activities'] = array_values(array_filter(
                $day['activities'],
                fn($activity) => $activity['id'] !== $id
            ));
        }
        unset($day);

        $this->syncTripToSession();
        $this->removeFromFavorites($id);
    }

    public function removeFromActivities($id)
    {
        $this->tripActivities = array_values(array_filter(
            $this->tripActivities,
            fn($activity) => $activity['id'] !== $id
        ));

        $this->syncTripToSession();
        $this->removeFromFavorites($id);
    }

    private function removeFromFavorites($id)
    {
        $favorites = session('favorite_activities', []);
        $favorites = array_values(array_filter(
            $favorites,
            fn($item) => $item['id'] !== $id
        ));
        session(['favorite_activities' => $favorites]);

        $this->dispatch('favorites-updated');
        session()->flash('success', 'Aktivität entfernt!');
    }

    private function syncTripToSession(bool $dispatchEvent = true)
    {
        session([
            'trip_planner' => [
                'name' => $this->tripName,
                'description' => $this->tripDescription,
                'use_days' => $this->useDays,
                'days' => $this->tripDays,
                'activities' => $this->tripActivities,
            ]
        ]);

        session(['favorite_activities' => $this->useDays
            ? collect($this->tripDays)->pluck('activities')->flatten(1)->toArray()
            : $this->tripActivities
        ]);

        if ($dispatchEvent) {
            $this->dispatch('favorites-updated');
        }
    }

    public function saveTrip()
    {
        $this->validate();
        session()->flash('success', 'Trip erfolgreich gespeichert!');
        $this->hideModal();
    }


    public function saveTripToDatabase()
    {
        $activities = $this->useDays
            ? collect($this->tripDays)->pluck('activities')->flatten(1)
            : collect($this->tripActivities);

        if ($activities->isEmpty()) {
            session()->flash('error', 'Bitte wähle mindestens eine Aktivität aus.');
            return;
        }

        $locations = $activities
            ->pluck('location_name')
            ->filter()
            ->unique()
            ->values();

        $locationString = match (count($locations)) {
            0 => 'unbekannten Orten',
            1 => $locations[0],
            2 => $locations->implode(' und '),
            default => $locations->slice(0, -1)->implode(', ') . ' und ' . $locations->last(),
        };

        $mainLocation = $locations->join(', ');
        $firstTitle = $activities->first()['title'] ?? 'Erlebnis';

        if (empty($this->tripName) || empty($this->tripDescription)) {
            $aiTexts = $this->generateTripTextsFromAI($locationString, $activities);
            $this->tripName = $this->tripName ?: $aiTexts['title'];
            $this->tripDescription = $this->tripDescription ?: $aiTexts['description'];
        }

        $this->validate([
            'tripName' => 'required|min:3|max:100',
            'tripDescription' => 'nullable|string|max:500',
        ]);

        $trip = \App\Models\ModTrip::updateOrCreate(
            ['id' => session('mod_trip_id')],
            [
                'user_id' => auth()->check() ? auth()->id() : null,
                'name' => $this->tripName,
                'description' => $this->tripDescription,
                'main_location' => $mainLocation,
                'use_days' => $this->useDays,
                'days' => $this->useDays ? $this->tripDays : [[
                    'name' => 'Gesamtliste',
                    'notes' => '',
                    'activities' => $this->tripActivities,
                ]],
                'is_public' => true,
            ]
        );

        session(['mod_trip_id' => $trip->id]);
        session()->flash('success', 'Trip gespeichert!');
        $this->dispatch('trip-saved');
    }

    private function generateTripTextsFromAI($locationString, $activities): array
    {
        $titles = collect($activities)->pluck('title')->unique()->take(5)->implode(', ');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepinfra.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                'input' => "[INST] Erstelle einen kreativen Titel und eine kurze Beschreibung (max. 40 Wörter) für eine Erlebnisreise durch folgende Städte: {$locationString}. Die Aktivitäten beinhalten: {$titles}. Gib beide Texte im JSON-Format mit den Schlüsseln 'title' und 'description' zurück. [/INST]",
                'max_new_tokens' => 150,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $json = json_decode($response->body(), true);
                if (isset($json['output'])) {
                    $clean = json_decode($json['output'], true);
                    return [
                        'title' => $clean['title'] ?? "Trip nach {$locationString}",
                        'description' => $clean['description'] ?? "Eine spannende Reise mit Aktivitäten in {$locationString}.",
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('AI Trip Text Generation failed: ' . $e->getMessage());
        }

        return [
            'title' => "Erlebnisreise nach {$locationString}",
            'description' => "Ein abwechslungsreicher Trip mit Aktivitäten wie {$titles} in {$locationString}.",
        ];
    }


    public function updated($propertyName)
    {
        if ($propertyName === 'useDays') {
            // Beim Umschalten den jeweils anderen Modus zurücksetzen
            if ($this->useDays) {
                // Von Liste zu Tagesmodus wechseln
                $this->tripDays = [[
                    'name' => 'Tag 1',
                    'notes' => '',
                    'activities' => $this->tripActivities,
                ]];
                $this->tripActivities = [];
            } else {
                // Von Tagesmodus zu Listenmodus wechseln
                $this->tripActivities = collect($this->tripDays)
                    ->pluck('activities')
                    ->flatten(1)
                    ->unique('id')
                    ->values()
                    ->toArray();
                $this->tripDays = [];
            }
        }

        if (
            str_starts_with($propertyName, 'tripDays') ||
            in_array($propertyName, ['tripName', 'tripDescription', 'tripActivities', 'useDays'])
        ) {
            $this->validateOnly($propertyName);
            $this->syncTripToSession(false);
        }
    }


    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.trip-planner');
    }
}
