<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocationSearchLocationActivities extends Component
{
    public $listBeach;
    public $listCitytravel;
    public $listSports;
    public $listIsland;
    public $listCulture;
    public $listNature;
    public $listWatersport;
    public $listWintersport;
    public $listMountainsport;
    public $dailyTemperature;

    public function __construct($listBeach=false, $listCitytravel=false, $listSports=false, $listIsland=false, $listCulture=false, $listNature=false, $listWatersport=false, $listWintersport=false, $listMountainsport=false, $dailyTemperature='')
    {

       $this->listBeach= $listBeach ;
       $this->listCitytravel= $listCitytravel;
       $this->dailyTemperature=  $dailyTemperature;
       $this->listSports= $listSports;
       $this->listIsland=$listIsland;
       $this->listCulture= $listCulture;
       $this->listNature=$listNature;
       $this->listWatersport=$listWatersport;
       $this->listWintersport=$listWintersport;
       $this->listMountainsport= $listMountainsport;
    }

    public function render()
    {
        $activities = '&nbsp;';
        $activities .= ($this->listBeach == 1) ? '<i class="fas fa-umbrella-beach fa-lg me-1" title="Strand"></i>' : '';
        $activities .= ($this->listCitytravel == 1) ? '<i class="fas fa-city fa-lg me-1" title="Städtereise"></i>' : '';
        $activities .= ($this->listSports == 1) ? '<i class="fas fa-biking fa-lg me-1" title="Sport"></i>' : '';
//        $activities .= ($this->listIsland == 1) ? '<i class="fas fa-landmark fa-lg me-1" alt="Inselurlaub" title="Inselurlaub "></i>' : '';
//        $activities .= ($this->listIsland == 1) ? '<i class="fa-solid fa-island-tropical" alt="Inselurlaub" title="Inselurlaub "></i>' : '';
        $activities .= ($this->listIsland == 1) ? '<img style="margin-top: -3px;height: 30px;" src="'.asset('img/insel-icon.png').' " als="Insel" title="Insel"/>' : '';

//        $activities .= ($this->listIsland == 1) ? '<i class="fas fa-island-tropical" alt="Inselurlaub" title="Inselurlaub "></i>' : '';
        $activities .= ($this->listCulture == 1) ? '<i class="fa fa-theater-masks fa-lg me-1" title="Kultur"></i>' : '';
        $activities .= ($this->listNature == 1) ? '<i class="fas fa-tree fa-lg me-1" title="Natur"></i>' : '';
        $activities .= ($this->listWintersport == 1 && $this->dailyTemperature < 3 ) ? '<i class="fas fa-person-skiing fa-lg me-1" title="Wintersport"></i>' : '';
        $activities .= ($this->listWatersport == 1) ? '<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>' : '';
//        $activities .= ($this->listWintersport == 1) ? '<i class="fas fa-skiing fa-lg me-1" title="Wintersport"></i>' : '';
        $activities .= ($this->listMountainsport == 1) ? '<i class="fas fa-hiking fa-lg me-1" title="Bergsport"></i>' : '';

        return $activities;
    }
}


