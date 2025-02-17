<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\WwdeLocation;

class WishlistButtonComponent extends Component
{
    public $locationId;
    public $inWishlist = false;
    public $slug;

    public function mount($locationId)
    {
        $this->locationId = $locationId;

        $location = WwdeLocation::find($locationId);

        if ($location) {
            // Falls kein Slug vorhanden ist, generiere ihn und speichere die Location
            if (empty($location->slug) && !empty($location->title)) {
                $location->slug = Str::slug($location->title);
                $location->save(); // Slug direkt in der Datenbank speichern
            }

            $this->slug = $location->slug;
        }

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
        return view('livewire.frontend.wishlist-select.wishlist-button-component', [
            'slug' => $this->slug,
        ]);
    }
}
