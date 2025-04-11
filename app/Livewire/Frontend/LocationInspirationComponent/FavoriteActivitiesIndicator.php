<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class FavoriteActivitiesIndicator extends Component
{
    public bool $showFavorites = false;
    public array $favorites = [];

    public function mount()
    {
        $this->loadFavorites();
    }

    public function toggleFavorites()
    {
        $this->showFavorites = !$this->showFavorites;

        if ($this->showFavorites) {
            $this->loadFavorites(); // Nur laden, wenn geöffnet
        }

    }
    
    #[On('favorites-updated')]
    public function refreshFavorites()
    {
        $this->loadFavorites();
        Log::debug('Favoriten aktualisiert:', $this->favorites);
    }

    public function removeFromFavorites($id)
    {
        $favorites = session('favorite_activities', []);
        $favorites = array_filter(
            $favorites,
            fn($item) => $item['id'] !== $id
        );
        $favorites = array_values($favorites);
        session(['favorite_activities' => $favorites]);
        $this->loadFavorites();
        session()->flash('success', 'Aus Favoriten entfernt!');
        Log::debug('Favorit entfernt:', ['id' => $id]);
    }

    public function clearFavorites()
    {
        session(['favorite_activities' => []]);
        $this->loadFavorites();
        session()->flash('success', 'Favoriten geleert!');
    }

    private function loadFavorites()
    {
        $this->favorites = session('favorite_activities', []);
    }

    public function getGroupedFavoritesProperty()
    {
        return collect($this->favorites)->groupBy(fn($item) => $item['location_name'] ?? 'Unbekannter Ort');
    }

    public function openTripPlanner()
    {
        $this->dispatch('open-trip-planner'); // Event zum Öffnen
    }

    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.favorite-activities-indicator');
    }
}
