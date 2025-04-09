<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\WwdeLocation;
use App\Models\AmusementParks;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class TripActivities extends Component
{
    public int $locationId;
    public string $locationTitle;
    public array $selectedActivities = [];
    public float $mapCenterLat = 50.110924;
    public float $mapCenterLon = 8.682127;
    public array $tripDays = [];
    public string $tripDescription = '';
    public string $tripName = '';
    public bool $isLoading = false;
    public string $savedDataPreview = '';
    public array $lastRemoved = [];
    public $location;

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->location = WwdeLocation::find($locationId); // Location-Daten laden
        $this->locationTitle = $this->location->title ?? 'Unbekannter Ort';
        $this->mapCenterLat = $this->location->lat ?? 50.110924; // Standardwert als Fallback
        $this->mapCenterLon = $this->location->lon ?? 8.682127; // Standardwert als Fallback

        if (empty($this->tripDays)) {
            $this->tripDays = [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
        }
    }

    public function addTripDay()
    {
        $this->tripDays[] = [
            'name' => 'Tag ' . (count($this->tripDays) + 1),
            'notes' => '',
            'activities' => [],
        ];
    }

    public function removeTripDay($index)
    {
        if (isset($this->tripDays[$index]) && count($this->tripDays) > 1) {
            unset($this->tripDays[$index]);
            $this->tripDays = array_values($this->tripDays);
        }
    }

    public function updateDayNotes($index, $notes)
    {
        if (isset($this->tripDays[$index])) {
            $this->tripDays[$index]['notes'] = $notes;
        }
    }

    public function moveActivityToDay(string $activityId, int $fromDay, int $toDay)
    {
        if (!isset($this->tripDays[$fromDay]) || !isset($this->tripDays[$toDay])) {
            Log::debug("Ungültige Indizes: fromDay=$fromDay, toDay=$toDay", $this->tripDays);
            return;
        }

        $activity = collect($this->tripDays[$fromDay]['activities'])->firstWhere('id', $activityId);
        if ($activity) {
            $this->tripDays[$fromDay]['activities'] = array_values(array_filter(
                $this->tripDays[$fromDay]['activities'],
                fn($a) => $a['id'] !== $activityId
            ));
            $this->tripDays[$toDay]['activities'][] = $activity;

            if (empty($this->tripDays[$fromDay]['activities']) && count($this->tripDays) > 1) {
                unset($this->tripDays[$fromDay]);
                $this->tripDays = array_values($this->tripDays);
            }
            Log::debug('TripDays after move:', $this->tripDays);
        }
    }

    public function moveActivityUp(string $activityId, int $dayIndex)
    {
        if (!isset($this->tripDays[$dayIndex]) || empty($this->tripDays[$dayIndex]['activities'])) {
            return;
        }

        $activities = $this->tripDays[$dayIndex]['activities'];
        $index = array_search($activityId, array_column($activities, 'id'));

        if ($index !== false && $index > 0) {
            $temp = $activities[$index - 1];
            $activities[$index - 1] = $activities[$index];
            $activities[$index] = $temp;
            $this->tripDays[$dayIndex]['activities'] = $activities;
        }
    }

    public function moveActivityDown(string $activityId, int $dayIndex)
    {
        if (!isset($this->tripDays[$dayIndex]) || empty($this->tripDays[$dayIndex]['activities'])) {
            return;
        }

        $activities = $this->tripDays[$dayIndex]['activities'];
        $index = array_search($activityId, array_column($activities, 'id'));

        if ($index !== false && $index < count($activities) - 1) {
            $temp = $activities[$index + 1];
            $activities[$index + 1] = $activities[$index];
            $activities[$index] = $temp;
            $this->tripDays[$dayIndex]['activities'] = $activities;
        }
    }

    public function toggleActivity(string $activity)
    {
        // Wenn die Aktivität bereits ausgewählt ist, wird sie entfernt (optionales Toggle-Verhalten)
        if (in_array($activity, $this->selectedActivities)) {
            $this->selectedActivities = [];
        } else {
            // Nur die neue Aktivität auswählen und alle anderen entfernen
            $this->selectedActivities = [$activity];
        }
    }

    public function addToTrip(string $id)
    {
        $activity = $this->activities->firstWhere('id', $id);
        if (!$activity) return;

        foreach ($this->tripDays as $day) {
            if (collect($day['activities'])->pluck('id')->contains($id)) {
                return;
            }
        }

        $newActivity = [
            'id' => $activity['id'],
            'title' => $activity['title'],
            'latitude' => $activity['latitude'],
            'longitude' => $activity['longitude'],
            'distance' => $activity['distance'],
            'duration' => $activity['duration'],
            'category' => $activity['category'],
        ];

        if (empty($this->tripDays)) {
            $this->tripDays[] = ['name' => 'Tag 1', 'notes' => '', 'activities' => [$newActivity]];
        } else {
            $lastIndex = array_key_last($this->tripDays) ?? 0;
            $this->tripDays[$lastIndex]['activities'][] = $newActivity;
        }

        $this->tripDays = array_values($this->tripDays);
        session()->flash('success', 'Zur Reise hinzugefügt!');
    }

    public function removeFromTrip(string $id)
    {
        foreach ($this->tripDays as $index => &$day) {
            $activity = collect($day['activities'])->firstWhere('id', $id);
            if ($activity) {
                $this->lastRemoved = ['dayIndex' => $index, 'activity' => $activity];
                $day['activities'] = array_values(array_filter(
                    $day['activities'],
                    fn($a) => $a['id'] !== $id
                ));
            }
        }
        unset($day);
        $this->tripDays = $this->tripDays;
    }

    public function undoRemove()
    {
        if (!empty($this->lastRemoved)) {
            $dayIndex = $this->lastRemoved['dayIndex'];
            $activity = $this->lastRemoved['activity'];
            if (isset($this->tripDays[$dayIndex])) {
                $this->tripDays[$dayIndex]['activities'][] = $activity;
                $this->lastRemoved = [];
                session()->flash('success', 'Aktivität wiederhergestellt!');
            }
        }
    }

    public function resetTrip()
    {
        $this->tripDays = [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
        $this->tripDescription = '';
        $this->tripName = '';
        $this->lastRemoved = [];
        session()->flash('success', 'Trip zurückgesetzt!');
    }

    public function exportToPDF()
    {
        $this->isLoading = true;

        $data = [
            'tripName' => $this->tripName,
            'tripDays' => $this->tripDays,
            'tripDescription' => $this->tripDescription,
            'locationTitle' => $this->locationTitle, // Hinzugefügt
        ];

        $pdf = Pdf::loadView('livewire.frontend.location-inspiration-component.trip-pdf', $data)
                  ->setPaper('A4', 'portrait');

        $fileName = $this->tripName ? "Trip_{$this->tripName}.pdf" : "Trip.pdf";

        $this->isLoading = false;

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $fileName
        );
    }

    public function sharePDF()
    {
        $this->isLoading = true;

        $data = [
            'tripName' => $this->tripName,
            'tripDays' => $this->tripDays,
            'tripDescription' => $this->tripDescription,
            'locationTitle' => $this->locationTitle,
        ];

        $pdf = Pdf::loadView('livewire.frontend.location-inspiration-component.trip-pdf', $data)
                  ->setPaper('A4', 'portrait');

        $fileName = $this->tripName ? "Trip_{$this->tripName}.pdf" : "Trip.pdf";
        $filePath = public_path('temp/' . $fileName);

        // Verzeichnis erstellen, falls nicht vorhanden
        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0755, true);
        }

        $pdf->save($filePath);

        $this->isLoading = false;

        // Dispatch an Event, um das Teilen im Frontend auszulösen
        $this->dispatch('share-pdf', ['fileUrl' => asset('temp/' . $fileName)]);
    }

    public function shareViaWhatsApp()
    {
        $this->isLoading = true;

        $message = "Mein Trip: *{$this->tripName}*\n";
        $message .= "Ort: {$this->locationTitle}\n";
        $message .= "Beschreibung: {$this->tripDescription}\n\n";

        foreach ($this->tripDays as $index => $day) {
            $message .= "*{$day['name']}*\n";
            foreach ($day['activities'] as $activity) {
                $message .= "- {$activity['title']} (Dauer: {$activity['duration']})\n";
            }
            if (!empty($day['notes'])) {
                $message .= "Notizen: {$day['notes']}\n";
            }
            $message .= "\n";
        }

        $encodedMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/?text={$encodedMessage}";

        $this->isLoading = false;

        $this->dispatch('open-url', ['url' => $whatsappUrl]);
    }

    public function getTotalDurationProperty()
    {
        $totalHours = 0;

        foreach ($this->tripDays as $day) {
            foreach ($day['activities'] as $activity) {
                $duration = $activity['duration'];
                if ($duration === '1 Stunde') {
                    $totalHours += 1;
                } elseif ($duration === '2–3 Stunden') {
                    $totalHours += 2.5;
                } elseif ($duration === 'Halbtags') {
                    $totalHours += 4;
                }
            }
        }

        return [
            'hours' => round($totalHours, 1),
            'percentage' => min(100, ($totalHours / 24) * 100),
        ];
    }

    public function getTripActivitiesProperty()
    {
        return collect($this->tripDays)->pluck('activities')->flatten(1);
    }

    public function getActivitiesProperty()
    {
        if (empty($this->selectedActivities)) {
            Log::debug('Keine Aktivitäten ausgewählt');
            return collect();
        }

        Log::debug('Selected Activities:', $this->selectedActivities);

        $activities = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->whereIn('uschrift', $this->selectedActivities)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'activity-' . $item->id,
                    'title' => $item->uschrift,
                    'description' => $item->text,
                    'category' => $item->category,
                    'text_type' => $item->text_type,
                    'image' => $item->image_url ?? 'https://via.placeholder.com/150',
                    'icon' => match (strtolower($item->category)) {
                        'architektur' => 'fa-landmark',
                        'vergnügungspark' => 'ti ti-rollercoaster',
                        default => 'fa-location-dot',
                    },
                    'duration' => ['1 Stunde', '2–3 Stunden', 'Halbtags'][rand(0, 2)],
                    'season' => ['Frühling', 'Sommer', 'Ganzjährig'][rand(0, 2)],
                    'rating' => rand(85, 99) . '% positiv',
                    'isRecommended' => rand(0, 1) === 1,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'distance' => $this->calculateDistance(
                        $this->mapCenterLat,
                        $this->mapCenterLon,
                        $item->latitude,
                        $item->longitude
                    ),
                ];
            })->all();

        Log::debug('ModLocationFilter Activities:', $activities);

        $amusementParks = AmusementParks::whereIn('name', $this->selectedActivities)
            ->get()
            ->map(function ($park) {
                $distance = $this->calculateDistance(
                    $this->mapCenterLat,
                    $this->mapCenterLon,
                    $park->latitude,
                    $park->longitude
                );

                if ($distance !== null && $distance <= 150) {
                    Log::debug('Freizeitpark gefunden:', [$park->name, $distance]);
                    return [
                        'id' => 'park-' . $park->id,
                        'title' => $park->name,
                        'description' => $park->description ?? 'Ein toller Freizeitpark!',
                        'category' => 'vergnügungspark',
                        'text_type' => null,
                        'image' => $park->logo_url ?? 'https://via.placeholder.com/150',
                        'icon' => 'ti ti-rollercoaster',
                        'duration' => 'Halbtags',
                        'season' => $park->opening_hours ? 'Ganzjährig' : 'Sommer',
                        'rating' => rand(85, 99) . '% positiv',
                        'isRecommended' => rand(0, 1) === 1,
                        'latitude' => $park->latitude,
                        'longitude' => $park->longitude,
                        'distance' => $distance,
                    ];
                }
                return null;
            })->filter()->all();

        Log::debug('Amusement Parks nach Filter:', $amusementParks);

        // Änderung: Sortiere nach 'title' statt 'distance'
        $result = collect(array_merge($activities, $amusementParks))->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);
        Log::debug('Kombinierte Aktivitäten (nach Titel sortiert):', $result->toArray());
        return $result;
    }

    public function getActivityFiltersProperty()
    {
        $filters = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->uschrift,
                    'category' => $item->category,
                    'btnClass' => match (strtolower($item->category)) {
                        'laufen', 'radfahren', 'wassersport', 'klettern' => 'btn-sport',
                        'vergnügungspark', 'familienpark', 'zoo' => 'btn-freizeitpark',
                        default => 'btn-erlebnis',
                    },
                    'icon' => match (strtolower($item->category)) {
                        'architektur' => 'fa-landmark',
                        'vergnügungspark' => 'ti ti-rollercoaster',
                        'vergnügungspark' => 'fa-roller-coaster',
                        'veranstaltungen' => 'fa-ticket',
                        'wissen' => 'fa-book',
                        'laufen' => 'fa-running',
                        'essen und trinken' => 'fa-utensils',
                        'aussicht' => 'fa-eye',
                        'nachtleben' => 'fa-cocktail',
                        'zoo' => 'fa-paw',
                        'natur' => 'fa-tree',
                        'radfahren' => 'fa-bicycle',
                        'entspannung' => 'fa-spa',
                        'shopping' => 'fa-shopping-bag',
                        'wandern' => 'fa-hiking',
                        'wassersport' => 'fa-water',
                        'familienpark' => 'fa-child',
                        'klettern' => 'fa-mountain',
                        'veranstaltungen' => 'fa-calendar',
                        'essen und trinken' => 'fa-utensils',
                        'aussicht' => 'fa-eye',
                        default => 'fa-location-dot',
                    },
                ];
            })->unique('title');

        $parks = AmusementParks::all()->map(function ($park) {
            $distance = $this->calculateDistance(
                $this->mapCenterLat,
                $this->mapCenterLon,
                $park->latitude,
                $park->longitude
            );

            if ($distance !== null && $distance <= 150) {
                Log::debug("Freizeitpark als Filter hinzugefügt: {$park->name}, Distance: $distance");
                return [
                    'title' => $park->name,
                    'category' => 'vergnügungspark',
                    'btnClass' => 'btn-freizeitpark',
                    'icon' => 'ti ti-rollercoaster',
                ];
            }
            return null;
        })->filter();

        // Sortiere nach 'title'
        $combinedFilters = $filters->merge($parks)->unique('title')->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);
        Log::debug('Kombinierte Filter:', $combinedFilters->toArray());

        return $combinedFilters;
    }

    #[On('saveTripToLocal')]
    public function saveTripToLocal()
    {
        $this->isLoading = true;
        foreach ($this->tripDays as &$day) {
            $day['activities'] = array_values($day['activities']);
        }
        unset($day);

        $tripData = [
            'locationId' => $this->locationId,
            'tripDays' => $this->tripDays,
            'tripDescription' => $this->tripDescription,
            'tripName' => $this->tripName,
        ];
        Log::debug('Trip wird gespeichert:', $tripData);
        $this->savedDataPreview = json_encode($tripData, JSON_PRETTY_PRINT);
        $this->dispatch('save-trip-local', $tripData);
        session()->flash('success', "Trip '{$this->tripName}' lokal gespeichert!");
        $this->isLoading = false;
    }

    #[On('loadTripFromLocal')]
    public function loadTripFromLocal($data = null)
    {
        $this->isLoading = true;
        if ($data) {
            $this->locationId = $data['locationId'] ?? $this->locationId;
            $loadedDays = is_array($data['tripDays']) && !empty($data['tripDays'])
                ? $data['tripDays']
                : [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
            foreach ($loadedDays as &$day) {
                $day['activities'] = array_values($day['activities']);
            }
            unset($day);
            $this->tripDays = $loadedDays;
            $this->tripDescription = $data['tripDescription'] ?? '';
            $this->tripName = $data['tripName'] ?? '';
            Log::debug('Trip geladen:', $this->tripDays);
            $this->savedDataPreview = json_encode($data, JSON_PRETTY_PRINT);
            session()->flash('success', "Trip '{$this->tripName}' geladen!");
        } else {
            $this->tripDays = [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
            $this->savedDataPreview = 'Keine Daten geladen';
            session()->flash('error', 'Keine Trip-Daten zum Laden gefunden.');
        }
        $this->dispatch('trip-loaded');
        $this->isLoading = false;
    }

    #[On('dragDropActivity')]
    public function handleDragDrop($activityId, $fromDay, $toDay)
    {
        $this->moveActivityToDay($activityId, $fromDay, $toDay);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return null;
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    public function render()
    {
        if (empty($this->tripDays)) {
            $this->tripDays = [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
        }

        $tripActivities = $this->tripActivities;

        $this->dispatch('trip-map-update', [
            'center' => ['lat' => $this->mapCenterLat, 'lon' => $this->mapCenterLon],
            'items' => $tripActivities->values(),
        ]);

        return view('livewire.frontend.location-inspiration-component.trip-activities', [
            'tripActivities' => $tripActivities,
        ]);
    }
}
