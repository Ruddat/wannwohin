<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocationActivities extends Component
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
        $style = 'style="margin-top: -3px;height: 30px;" class="ms-3 text-color-grey bold"';
        $div = '<div class="col-4 d-flex border-right justify-content-start">';
        $activities = '';
        $activities .= ($this->listBeach == 1) ? $div.'<i class="fas fa-umbrella-beach fa-lg me-1" title="Strand"></i><span '.$style.'>Strand</span></div>' : '';
        $activities .= ($this->listCitytravel == 1) ? $div.'<i class="fas fa-city fa-lg me-1" title="Städtereise"></i><span '.$style.'>Städtereise</span></div>' : '';
//        $activities .= ($this->listSports == 1) ? $div.'<i class="fas fa-biking fa-lg me-1" title="Sport"></i><span '.$style.'>Sport</span></div>' : '';
        $activities .= ($this->listIsland == 1) ? $div.'<img style="margin-top: -3px;height: 30px;" src="'.asset('img/insel-icon.png').' " als="Insel" title="Insel"/><span '.$style.'>Insel</span></div>' : '';
        $activities .= ($this->listCulture == 1) ? $div.'<i class="fa fa-theater-masks fa-lg me-1" title="Kultur"></i><span '.$style.'>Kultur</span></div>' : '';
        $activities .= ($this->listNature == 1) ? $div.'<i class="fas fa-tree fa-lg me-1" title="Natur"></i><span '.$style.'>Natur</span></div>' : '';
        $activities .= ($this->listWintersport == 1 && $this->dailyTemperature < 3 ) ? $div.'<i class="fas fa-skiing fa-lg me-1" title="Wintersport"></i><span '.$style.'>Wintersport</span></div>' : '';
        $activities .= ($this->listWatersport == 1) ? $div.'<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i><span '.$style.'>Wassersport</span></div>' : '';
//        $activities .= ($this->listWintersport == 1) ? $div.'<i class="fas fa-skiing fa-lg me-1" title="Wintersport"></i><span '.$style.'>Wintersport</span></div>' : '';
        $activities .= ($this->listMountainsport == 1) ? $div.'<i class="fas fa-hiking fa-lg me-1" title="Bergsport"></i><span '.$style.'>Bergsport</span></div>' : '';

        return $activities;
    }
}


