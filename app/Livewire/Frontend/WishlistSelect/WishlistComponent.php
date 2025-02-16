<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;
use App\Models\WwdeLocation;

class WishlistComponent extends Component
{
    public $wishlist = [];
    public $showWishlist = false;

    // 🟢 Listener für das Event "wishlistUpdated"
    protected $listeners = ['wishlistUpdated' => 'updateWishlist'];

    public function mount()
    {
        $this->updateWishlist();
    }

    public function updateWishlist()
    {
        $this->wishlist = session()->get('wishlist', []);
    }

    public function toggleWishlist()
    {
        $this->showWishlist = !$this->showWishlist;
    }

    public function removeFromWishlist($locationId)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_diff($wishlist, [$locationId]);

        session()->put('wishlist', $wishlist);
        $this->wishlist = $wishlist;

        // 🟢 Event an alle Komponenten senden, damit Buttons sich aktualisieren
        $this->dispatch('wishlistUpdated')->to(WishlistButtonComponent::class);
    }
    
    public function clearWishlist()
    {
        session()->forget('wishlist');
        $this->wishlist = [];

        // 🟢 Event zum Aktualisieren aller Buttons
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-select.wishlist-component', [
            'locations' => WwdeLocation::whereIn('id', $this->wishlist)->get(),
        ]);
    }
}
