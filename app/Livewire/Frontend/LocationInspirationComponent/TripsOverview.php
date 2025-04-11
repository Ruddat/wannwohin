<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use App\Models\ModTrip;
use Livewire\Component;

class TripsOverview extends Component
{
    public $trips;

    public function mount()
    {
        $this->trips = ModTrip::where('is_public', true)->latest()->take(20)->get();
    }

    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.trips-overview')
        ->layout('layouts.main');
    }
}
