<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Livewire\Component;
use App\Models\WwdeLocation;

class WishlistCompareComponent extends Component
{
    public $compareList = [];

    public function mount($slugs)
    {
        // Slugs aus der URL extrahieren (z.B. "berlin-hamburg-muenchen" -> ['berlin', 'hamburg', 'muenchen'])
        $slugArray = explode('-', $slugs);

        // IDs aus den Slugs holen
        $this->compareList = WwdeLocation::whereIn('slug', $slugArray)->pluck('id')->toArray();
    }

    public function removeFromCompare($locationId)
    {
        $this->compareList = array_diff($this->compareList, [$locationId]);
    }

    public function clearCompare()
    {
        $this->compareList = [];
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-select.wishlist-compare-component', [
            'locations' => WwdeLocation::whereIn('id', $this->compareList)->get(),
        ]);
    }
}
