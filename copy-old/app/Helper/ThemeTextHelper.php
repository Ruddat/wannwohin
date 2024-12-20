<?php

namespace App\Helper;

use App\Models\Continent;
use URL;
use Illuminate\Http\Request;

class ThemeTextHelper
{
    static $MONTH = [
        1  => 'Jan',
        2  => 'Feb',
        3  => 'März',
        4  => 'Apr',
        5  => 'Mai',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Aug',
        9  => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Dez',
    ];
    public static function monthArray2String($array): string
    {
        if(is_null($array)) return "";
        asort($array);
        $string = [[self::$MONTH[$array[0]]]];
        $number = $array[0];
        foreach ($array as $key => $item) {
            if ($key === 0)
                continue;
            if($number !== $item-1)
            {
                $string[] = [self::$MONTH[$item]];
            }
            else {
                $string[count($string)-1][1] = self::$MONTH[$item];
            }
            $number = $item;
        }
//        dd($string);
        $result = '';
        foreach ($string as $key => $month)
        {
            $result .= $month[0];
            if (isset($month[1]))
                $result .= '-'.$month[1];
            if ($key !== count($string)-1)
                $result .= ', ';
        }
        return $result;
    }

    public static function SelectContinents(){
        $segment_posts = Request()->segment(1); //returns 'posts'
        $continents = Continent::all();
        $selected = (Request()->segment(1)!="" &&  Request()->segment(2)==null) ? Request()->segment(1): '';
        $url = URL::to('/');
        $select= '<select data-url="'.$url.'"  class="continent-select-header form-select form-select-icon-light form-control bg-primary mb-3"><option value=" ">Kontinent auswählen</option>';
        foreach($continents as $continent) {
            $select .= '<option value="' . $continent->alias . '" '.(($selected == $continent->alias)? 'selected' : '' ).'>' . $continent->title . '</option>';
        }
        $select .='</select>';
        return $select;
    }
}
