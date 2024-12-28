<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\WwdeLocation;

class LocationActivities extends Component
{
    public $location;

    /**
     * Erzeugt eine Instanz der LocationActivities-Komponente.
     *
     * @param int $locationId
     */
    public function __construct($locationId)
    {
        $this->location = WwdeLocation::find($locationId);

        if (!$this->location) {
            throw new \Exception("Location with ID {$locationId} not found.");
        }
    }

    /**
     * Rendert die View für die Komponente.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.location-activities');
    }
}
