<?php

return [
    'daily_temp' => [
        'min' => -40,
        'max' => 45,
        'title' => 'Tagestemperatur',
        'icon' => 'fas fa-cloud-sun',
        'unit' => 'C',
        'step' => 5,
        'values' => [0, 5, 10, 15, 20, 25, 30, 35]
    ],
    'night_temp' =>  [
        'min' => -45,
        'max' => 40,
        'title' => 'Nachttemperatur',
        'icon' => 'fas fa-cloud-moon',
        'unit' => 'C',
        'step' => 5,
        'values' => [0, 5, 10, 15, 20, 25, 30, 35]
    ],
    'water_temp' =>  [
        'min' => 0,
        'max' => 35,
        'title' => 'Wassertemperatur',
        'icon' => 'fas fa-thermometer-half',
        'unit' => 'C',
        'step' => 5,
        'values' => [10, 15, 18, 20, 22, 24, 26, 28]
    ],
    'sunshine_per_day' =>
        [
            'min' => 0,
            'max' => 22,
            'title' => 'Sonnenstunden',
            'icon' => 'fas fa-sun',
            'unit' => ' C',
            'step' => 2,
            'values' => [2, 3, 4, 5, 6, 7, 8],
            ],
    'rainy_days' =>[
        'min' => 0,
        'max' => 28,
        'title' => 'Regentage',
        'icon' => 'fas fa-umbrella',
        'unit' => ' T',
        'step' => 2,
        'values' =>  [2, 4, 6, 8, 10, 12, 14]
    ],
    'humidity' => [
        'min' => 0,
        'max' => 100,
        'title' => 'Luftfeuchtigkeit',
        'icon' => 'fas fa-tint',
        'unit' => ' %',
        'step' => 10,
        'values' => [10, 20, 30, 40, 50, 60, 70, 80, 90]
    ],
    'flight_duration' =>[
        '1'=>[
            'unit'=> 'h',
            'title' => 1,
            'operator' => '<=',
            'value' => 1
        ],
        '2'=>[
            'unit'=> 'h',
            'title' => 2,
            'operator' => '<=',
            'value' => 2
        ],

        '3'=>[
            'unit'=> 'h',
            'title' => 3,
            'operator' => '<=',
            'value' => 3
        ],
        '4'=>[
            'unit'=> 'h',
            'title' => 4,
            'operator' => '<=',
            'value' => 4
        ],
        '5'=>[
            'unit'=> 'h',
            'title' => 5,
            'operator' => '<=',
            'value' => 5
        ],
        '6'=>[
            'unit'=> 'h',
            'title' => 6,
            'operator' => '<=',
            'value' => 6
        ],
        '7'=>[
            'unit'=> 'h',
            'title' => 7,
            'operator' => '<=',
            'value' => 7
        ],
        '8'=>[
            'unit'=> 'h',
            'title' => 8,
            'operator' => '<=',
            'value' => 8
        ],
        '9'=>[
            'unit'=> 'h',
            'title' => 9,
            'operator' => '<=',
            'value' => 9
        ],
        '10'=>[
            'unit'=> 'h',
            'title' => 10,
            'operator' => '<=',
            'value' => 10
        ],
        '11'=>[
            'unit'=> 'h',
            'title' => '> 10',
            'operator' => '>=',
            'value' => 10
        ],
    ],
    'distance_to_destination' =>[
        '1'=>[
            'unit'=> 'km',
            'title' => 500,
            'operator' => '<=',
            'value' => 500
        ],
        '2'=>[
            'unit'=> 'km',
            'title' => 1000,
            'operator' => '<=',
            'value' => 1000
        ],

        '3'=>[
            'unit'=> 'km',
            'title' => 1500,
            'operator' => '<=',
             'value' => 1500
        ],
        '4'=>[
            'unit'=> 'km',
            'title' => 2000,
            'operator' => '<=',
            'value' => 2000
        ],
        '5'=>[
            'unit'=> 'km',
            'title' => 3000,
            'operator' => '<=',
            'value' => 3000
        ],
        '6'=>[
            'unit'=> 'km',
            'title' => 5000,
            'operator' => '<=',
            'value' => 5000
        ],
          '7'=>[
            'unit'=> 'km',
            'title' => 7500,
            'operator' => '<=',
              'value' => 7500
        ],
          '8'=>[
            'unit'=> 'km',
            'title' => 10000,
            'operator' => '<=',
              'value' => 10000
        ],
          '9'=>[
            'unit'=> 'km',
            'title' => '> 10000',
            'operator' => '>=',
            'value' => 10000
        ],
    ],
    'languages' =>[
        "afrikaans",
        "arabisch",
        "armenisch",
        "aymara",
        "baskisch",
        "bengalisch",
        "bosnisch",
        "bulgarisch",
        "burmesisch",
        "chinesisch",
        "deutsch",
        "dänisch",
        "englisch",
        "estnisch",
        "finnisch",
        "französisch",
        "galego",
        "griechisch",
        "haitianisch",
        "indonesisch",
        "irisch",
        "italienisch",
        "japanisch",
        "katalanisch",
        "koreanisch",
        "kroatisch",
        "kroatisch",
        "ladino",
        "lettisch",
        "litauisch",
        "melanesische",
        "neu-hebräisch",
        "niederländisch",
        "norwegisch",
        "polnisch",
        "portugiesisch",
        "quechua",
        "rarotonganische",
        "rumänisch",
        "russisch",
        "samisch",
        "schwedisch",
        "serbisch",
        "serbokroatisch",
        "slowakisch",
        "sotho",
        "spanisch",
        "swazi",
        "thai",
        "tschechisch",
        "türkisch",
        "ukrainisch",
        "ungarisch",
        "vietnamesisch",
        "weißrussisch",
        "xhosa",
        "zulu"

    ],
    'preis_tendenz' =>[
        'unit' => '',
        'values' => [
            'low' => 'Niedrig',
            'middle' => 'Mittel',
            'high' => 'Hoch',
            ]
    ],
    'climate_lnam' =>[
        'unit' => '',
        'values' => [
            'Tropische Zone' => 'Tropische Zone',
            'Subtropische Zone' => 'Subtropische Zone',
            'Gemäßigte Zone' => 'Gemäßigte Zone',
            'Subpolare Zone' => 'Subpolare Zone',
            'Polare Zone' => 'Polare Zone',
            ]
    ]
];
