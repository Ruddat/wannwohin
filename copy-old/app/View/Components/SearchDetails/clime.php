<?php

namespace App\View\Components\SearchDetails;

use Illuminate\View\Component;

class Clime extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $title,
        public string $icon,
        public string $configPath,
        public string $selectName,
        public string $preWord = 'min',
        public string $afterWord = ' °C',
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.search-details.clime');
    }
}
