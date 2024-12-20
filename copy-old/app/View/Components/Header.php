<?php

namespace App\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class Header extends Component
{
    public $bgImg;
    public $mainImg;
    public $mainText;
    public $panoramaLocationPicture;
    public $mainLocationPicture;
    public $panoramaLocationText;

    public function __construct($panoramaLocationPicture=null, $mainLocationPicture=null, $panoramaLocationText=null)
    {
        $randomId       =   rand(1,5);
        $this->bgImg = ($panoramaLocationPicture != null && File::exists($panoramaLocationPicture))? asset($panoramaLocationPicture):  asset('img/startpage/0'.$randomId.'_beste_reisezeit_b.webp');
        $this->mainImg = ($mainLocationPicture != null && File::exists($mainLocationPicture)) ? asset($mainLocationPicture):  asset('img/startpage/0'.$randomId.'_beste_reisezeit_s.webp');
        $this->mainText = ($panoramaLocationText != null)? $panoramaLocationText: $this->getMainText($randomId);
    }

    public function render()
    {
        return view('components.header');
    }

    private function getMainText(int $randomId)
    {
        $mainText = array(
            1 =>    '<div class="txt1"><span>DIE SEELE</span><span>MAL RICHTIG</span><span>BAUMELN</span><span>LASSEN</span></div>',
            2 =>    '<div class="txt2"><span>DIE BESTE</span><span>ZEIT FÜR</span><span>URLAUB</span><span>IST JETZT</span></div>',
            3 =>    '<div class="txt3"><span>WOHIN DU</span><span>SCHON IMMER</span><span>MAL WOLLTEST</span></div>',
            4 =>    '<div class="txt4"><span>FREMDE</span><span>MENSCHEN</span><span>UND KULTUREN</span><span>KENNENLERNEN</span></div>',
            5 =>    '<div class="txt5"><span>EINDRUCKSVOLLE</span><span>NATURSCHAUSPIELE</span><span>ERLEBEN</span></div>'
            );

        return $mainText[$randomId];
    }
}
