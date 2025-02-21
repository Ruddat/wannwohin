<?php

namespace App\Helpers;



if (!function_exists('getIconClass')) {
    function getIconClass($textType)
    {
        $icons = [
            'wetter' => 'fas fa-sun',
            'erlebnis' => 'fas fa-map-marked-alt',
            'sport' => 'fas fa-dumbbell',
            'freizeitpark' => 'fas fa-gamepad',
            'inspiration' => 'fas fa-lightbulb',
        ];

        return $icons[$textType] ?? 'fas fa-question-circle';
    }
}


