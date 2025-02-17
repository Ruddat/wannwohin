<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;
use App\Models\WwdeLocation;

class WishlistCompareComponent extends Component
{
    public $compareList = [];

    public function mount()
    {
        // Wishlist-IDs aus der Session holen
        $this->compareList = session()->get('wishlist', []);
    }

    public function removeFromCompare($locationId)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_diff($wishlist, [$locationId]);

        session()->put('wishlist', $wishlist);
        $this->compareList = $wishlist;

        // 🟢 Event zum Aktualisieren der Wishlist-Komponente auslösen
        $this->dispatch('wishlistUpdated');
    }

    public function clearCompare()
    {
        session()->forget('wishlist');
        $this->compareList = [];

        // 🟢 Event zum Aktualisieren der Wishlist-Komponente auslösen
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-select.wishlist-compare-component', [
            'locations' => WwdeLocation::whereIn('id', $this->compareList)->get(),
        ]);
    }
}
