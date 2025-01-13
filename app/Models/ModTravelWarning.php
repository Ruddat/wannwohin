<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModTravelWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'severity',
        'issued_at',
        'iso2',
        'iso3',
    ];
}
