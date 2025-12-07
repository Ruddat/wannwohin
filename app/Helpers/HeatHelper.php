<?php

/* Number Format */
function nf($v) {
    return $v !== null ? number_format($v, 1, ',', '.') : '-';
}

/* Temperatur */
function heat_temp($v) {
    return match(true) {
        $v === null => '',
        $v < 5   => 'heat-temp-very-cold',
        $v < 12  => 'heat-temp-cold',
        $v < 20  => 'heat-temp-mild',
        $v < 28  => 'heat-temp-warm',
        default  => 'heat-temp-hot',
    };
}

/* Wassertemperatur */
function heat_water($v) {
    return match(true) {
        $v === null => '',
        $v < 10 => 'heat-water-cold',
        $v < 20 => 'heat-water-mid',
        default => 'heat-water-warm'
    };
}

/* Luftfeuchtigkeit */
function heat_humidity($v) {
    return match(true) {
        $v < 40 => 'heat-humidity-low',
        $v < 70 => 'heat-humidity-mid',
        default => 'heat-humidity-high',
    };
}

/* Sonne */
function heat_sun($v) {
    return match(true) {
        $v < 3 => 'heat-sun-low',
        $v < 6 => 'heat-sun-mid',
        default => 'heat-sun-high'
    };
}

/* Regentage */
function heat_rain($v) {
    return match(true) {
        $v < 5 => 'heat-rain-low',
        $v < 12 => 'heat-rain-mid',
        default => 'heat-rain-high',
    };
}

/* Reiseindex */
function heat_index($v) {
    return match(true) {
        $v >= 8 => 'heat-index-high',
        $v >= 5 => 'heat-index-mid',
        default => 'heat-index-low',
    };
}

/* Regenwahrscheinlichkeit */
function heat_rain_prob($v) {
    return match(true) {
        $v < 20 => 'heat-rain-prob-low',
        $v < 50 => 'heat-rain-prob-mid',
        default => 'heat-rain-prob-high',
    };
}

/* UV */
function heat_uv($v) {
    return match(true) {
        $v < 3 => 'heat-uv-low',
        $v < 6 => 'heat-uv-mid',
        default => 'heat-uv-high',
    };
}

/* Komfort */
function heat_comfort($v) {
    return match(true) {
        $v >= 8 => 'heat-comfort-high',
        $v >= 5 => 'heat-comfort-mid',
        default => 'heat-comfort-low',
    };
}

/* Wind */
function heat_wind($v) {
    return match(true) {
        $v < 10 => 'heat-wind-low',
        $v < 25 => 'heat-wind-mid',
        default => 'heat-wind-high',
    };
}

/* BESTE REISEZEIT */
function best_month($monthId) {
    return ($monthId >= 6 && $monthId <= 9) ? 'best-month' : '';
}
