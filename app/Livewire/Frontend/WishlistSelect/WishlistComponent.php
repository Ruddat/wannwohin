<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;
use App\Models\WwdeLocation;

class WishlistComponent extends Component
{
    public $wishlist = [];
    public $showWishlist = false;

    // 🟢 Event-Listener für Wishlist-Updates
    protected $listeners = [
        'wishlistUpdated' => 'updateWishlist',
        'removeFromWishlist' => 'removeFromWishlist'
    ];

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

        $this->dispatch('wishlistUpdated');
    }

    public function clearWishlist()
    {
        session()->forget('wishlist');
        $this->wishlist = [];

        $this->dispatch('wishlistUpdated');
    }

    // 🆕 Methode für Vergleichs-Button
    public function compareLocations()
    {
        if (count($this->wishlist) > 1) {
            return redirect()->route('compare', ['ids' => implode(',', $this->wishlist)]);
        }
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-select.wishlist-component', [
            'locations' => WwdeLocation::whereIn('id', $this->wishlist)->get(),
        ]);
    }
}
