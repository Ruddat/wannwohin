<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use App\Models\ModLocationFilter;

class TripActivities extends Component
{
    public int $locationId;
    public string $locationTitle;
    public array $selectedActivities = [];
    public float $mapCenterLat = 50.110924;
    public float $mapCenterLon = 8.682127;
    public array $tripDays = [];
    public string $tripDescription = ''; // Neue Eigenschaft für die Kurzbeschreibung

    public function mount()
    {
        $this->tripDays = [['name' => 'Tag 1', 'notes' => '', 'activities' => []]];
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
        if (isset($this->tripDays[$index])) {
            unset($this->tripDays[$index]);
            $this->tripDays = array_values($this->tripDays);
        }
    }

    public function updateDayNotes($index, $notes)
    {
        if (isset($this->tripDays[$index])) {
            $this->tripDays[$index]['notes'] = $notes;
            $this->tripDays = $this->tripDays; // Erzwingt Reaktivität
        }
    }

    public function moveActivityToDay(string $activityId, int $fromDay, int $toDay)
    {
        if (!isset($this->tripDays[$fromDay]) || !isset($this->tripDays[$toDay])) {
            return;
        }

        $activity = collect($this->tripDays[$fromDay]['activities'])->firstWhere('id', $activityId);
        if ($activity) {
            $this->tripDays[$fromDay]['activities'] = array_filter(
                $this->tripDays[$fromDay]['activities'],
                fn($a) => $a['id'] !== $activityId
            );
            $this->tripDays[$toDay]['activities'][] = $activity;
            $this->tripDays = array_values($this->tripDays);
        }
    }

    public function moveActivityUp(string $activityId, int $dayIndex)
    {
        if (!isset($this->tripDays[$dayIndex])) return;

        $activities = $this->tripDays[$dayIndex]['activities'];
        $index = array_search($activityId, array_column($activities, 'id'));
        if ($index !== false && $index > 0) {
            $temp = $activities[$index - 1];
            $activities[$index - 1] = $activities[$index];
            $activities[$index] = $temp;
            $this->tripDays[$dayIndex]['activities'] = $activities;
            $this->tripDays = $this->tripDays;
        }
    }

    public function moveActivityDown(string $activityId, int $dayIndex)
    {
        if (!isset($this->tripDays[$dayIndex])) return;

        $activities = $this->tripDays[$dayIndex]['activities'];
        $index = array_search($activityId, array_column($activities, 'id'));
        if ($index !== false && $index < count($activities) - 1) {
            $temp = $activities[$index + 1];
            $activities[$index + 1] = $activities[$index];
            $activities[$index] = $temp;
            $this->tripDays[$dayIndex]['activities'] = $activities;
            $this->tripDays = $this->tripDays;
        }
    }

    public function toggleActivity(string $activity)
    {
        if (in_array($activity, $this->selectedActivities)) {
            $this->selectedActivities = array_diff($this->selectedActivities, [$activity]);
        } else {
            $this->selectedActivities[] = $activity;
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
        ];

        $lastIndex = array_key_last($this->tripDays) ?? 0;
        $this->tripDays[$lastIndex]['activities'][] = $newActivity;
        $this->tripDays = $this->tripDays;

        session()->flash('success', 'Zur Reise hinzugefügt!');
    }

    public function removeFromTrip(string $id)
    {
        foreach ($this->tripDays as $index => $day) {
            $this->tripDays[$index]['activities'] = array_filter(
                $day['activities'],
                fn($a) => $a['id'] !== $id
            );
        }
        $this->tripDays = $this->tripDays;
    }

    public function getTripActivitiesProperty()
    {
        return collect($this->tripDays)->pluck('activities')->flatten(1);
    }

    public function getActivitiesProperty()
    {
        $query = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1);

        if (!empty($this->selectedActivities)) {
            $query->whereIn('uschrift', $this->selectedActivities);
        } else {
            return collect();
        }

        return $query->get()->map(function ($item) {
            return [
                'id' => 'activity-' . $item->id,
                'title' => $item->uschrift,
                'description' => $item->text,
                'category' => $item->category,
                'text_type' => $item->text_type,
                'image' => $item->image_url ?? 'https://via.placeholder.com/150',
                'icon' => match (strtolower($item->category)) {
                    'architektur' => 'fa-landmark',
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
        })->sortBy('distance');
    }

    public function getActivityFiltersProperty()
    {
        return ModLocationFilter::where('location_id', $this->locationId)
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
                        default => 'fa-location-dot',
                    },
                ];
            })
            ->unique('title');
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
