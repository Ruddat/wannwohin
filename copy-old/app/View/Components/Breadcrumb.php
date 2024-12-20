<?php

namespace App\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class Breadcrumb extends Component
{
    public $breadcrumbPaths;
    public $hidePath;
    /**
     * @var false
     */
    public $breadcrumbShowStatus;

    public function __construct($hidePath = null)
    {
        $url_parts = request()->segments();
        if (!$hidePath and count($url_parts) > 0) {
            $this->breadcrumbShowStatus = true;
            if ($url_parts[0] == 'urlaub') {
                $this->breadcrumbPaths = $this->handleSpecialLocationsPath($url_parts);
            } else {
                $this->breadcrumbPaths = $this->handleBreadcrumbPath($url_parts);
            }
        } else {
            $this->breadcrumbShowStatus = false;
        }
    }

    public function render()
    {
        return view('components.breadcrumb');
    }

    private function handleBreadcrumbPath($url_parts)
    {
//        request()->segment(count(request()->segments()))
//        last(request()->segments())
//       dd(basename(request()->path()));
//       dd(url()->full());
//        $BreadcrumbPath= array();
        $total_seg = count($url_parts);
        $current_seg = 0;
        $path_seg = '';
        foreach ($url_parts as $part) {
            $path_seg .= "/" . $part;
            $current_seg++;
            $BreadcrumbPath[] = array(
                'class' => ($current_seg == $total_seg) ? 'active' : '',
                'full_url' => url($path_seg),
                'title' => $part
            );
        }
        return $BreadcrumbPath;
    }

    private function handleSpecialLocationsPath(array $url_parts)
    {
        $BreadcrumbPath[] = array(
            'class' => 'active',
            'full_url' => url($url_parts[1]),
            'title' => $url_parts[1] . " urlaub"
        );
        return $BreadcrumbPath;
    }
}
