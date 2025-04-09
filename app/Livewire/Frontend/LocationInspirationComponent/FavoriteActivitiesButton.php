<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class FavoriteActivitiesButton extends Component
{
    public $activityId;
    public $activityTitle;
    public $activityData;
    public bool $isFavorite = false;

    public function mount($activity)
    {
        $this->activityId = $activity['id'];
        $this->activityTitle = $activity['title'];
        $this->activityData = $activity;

        // Prüfe, ob die Aktivität bereits in der Session ist
        $favorites = session('favorite_activities', []);
        $this->isFavorite = collect($favorites)->pluck('id')->contains($this->activityId);
    }

    public function toggleFavorite()
    {
        $favorites = session('favorite_activities', []);

        if ($this->isFavorite) {
            $favorites = array_filter(
                $favorites,
                fn($item) => $item['id'] !== $this->activityId
            );
            $favorites = array_values($favorites);
            session(['favorite_activities' => $favorites]);
            $this->isFavorite = false;
            session()->flash('success', "{$this->activityTitle} aus Favoriten entfernt!");
            Log::debug('Favorit entfernt:', ['id' => $this->activityId]);
        } else {
            if (!collect($favorites)->pluck('id')->contains($this->activityId)) {
                $favorites[] = $this->activityData;
                session(['favorite_activities' => $favorites]);
                $this->isFavorite = true;
                session()->flash('success', "{$this->activityTitle} zu Favoriten hinzugefügt!");
                Log::debug('Favorit hinzugefügt:', $this->activityData);
            }
        }

        // Event auslösen, um Indicator zu aktualisieren
        $this->dispatch('favorites-updated');
    }

    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.favorite-activities-button');
    }
}
