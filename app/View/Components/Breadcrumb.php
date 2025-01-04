<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $breadcrumbs;

    public function __construct()
    {
        // Breadcrumbs aus der View teilen
        $this->breadcrumbs = view()->shared('breadcrumbs', []);
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
