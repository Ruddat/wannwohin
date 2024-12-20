<?php

return array(

    'cache' => 120,
    'apiurl' => 'https://api.openweathermap.org/data/2.5/',
//    'appid' => '1a0d3925d52c1535ca2f448c66f2f321',
    'appid' => '7ad92c861d97885c93c11bfa0084ad6a',

    'defaults' => array(
        'style' => 'default',
        'query' => null,
        'days'  => 4,
        'units' => 'metric',
        'night' => 'no',
        'date'  => 'l',
        'lang'  => 'de'
    ),

    'view' => 'widgets/weather',

);

