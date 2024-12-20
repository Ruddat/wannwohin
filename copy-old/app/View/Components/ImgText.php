<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImgText extends Component
{
    public $imgContent;
    public $contentClass;

    public function __construct($imgContent=null,$contentClass=null)
    {
        $this->imgContent  = $imgContent;
        $this->contentClass  = $contentClass;
    }

    public function render()
    {
        return view('components.img-text');
    }
}
