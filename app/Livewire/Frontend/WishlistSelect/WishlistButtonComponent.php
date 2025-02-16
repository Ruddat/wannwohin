<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;

class WishlistButtonComponent extends Component
{
    public $locationId;
    public $inWishlist = false;

    public function mount($locationId)
    {
        $this->locationId = $locationId;

      //dd($locationId);
        $this->inWishlist = in_array($locationId, session()->get('wishlist', []));
    }

    public function toggleWishlist()
    {
        $wishlist = session()->get('wishlist', []);

        if ($this->inWishlist) {
            $wishlist = array_diff($wishlist, [$this->locationId]);
        } else {
            $wishlist[] = $this->locationId;
        }

        session()->put('wishlist', $wishlist);
        $this->inWishlist = !$this->inWishlist;

        // 🟢 Event zum Aktualisieren der Wishlist-Komponente auslösen
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-select.wishlist-button-component');
    }
}
