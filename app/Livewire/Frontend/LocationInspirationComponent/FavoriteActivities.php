<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class FavoriteActivities extends Component
{
    public array $favorites = [];
    public bool $isLoading = false;

    public function mount()
    {
        $this->favorites = session('favorite_activities', []);
    }

    #[On('add-to-favorites')]
    public function addToFavorites($activity)
    {
        try {
            // Prüfe, ob $activity ein Array ist und die benötigten Schlüssel enthält
            if (!is_array($activity) || !isset($activity['id']) || !isset($activity['title'])) {
                throw new \Exception('Ungültiges Activity-Format: ' . json_encode($activity));
            }

            if (!collect($this->favorites)->pluck('id')->contains($activity['id'])) {
                $this->favorites[] = $activity;
                session(['favorite_activities' => $this->favorites]);
                session()->flash('success', "{$activity['title']} zu Favoriten hinzugefügt!");
                Log::debug('Favorit hinzugefügt:', $activity);
            }
        } catch (\Exception $e) {
            Log::error('Fehler beim Hinzufügen zu Favoriten: ' . $e->getMessage());
            session()->flash('error', 'Fehler beim Hinzufügen zu Favoriten.');
        }
        dd($this->favorites);
    }

    public function removeFromFavorites($id)
    {
        $this->favorites = array_filter(
            $this->favorites,
            fn($item) => $item['id'] !== $id
        );
        $this->favorites = array_values($this->favorites);
        session(['favorite_activities' => $this->favorites]);
        session()->flash('success', 'Aus Favoriten entfernt!');
    }

    public function clearFavorites()
    {
        $this->favorites = [];
        session(['favorite_activities' => $this->favorites]);
        session()->flash('success', 'Favoriten geleert!');
    }

    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.favorite-activities');
    }
}
