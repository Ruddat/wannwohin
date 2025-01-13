<?php

namespace App\Models;

use App\Models\WwdeCountry;
use Illuminate\Database\Eloquent\Model;

class ModElectricStandards extends Model
{

    protected $table = 'mod_electric_standards';

    protected $casts = [
        'plug_images' => 'array', // JSON-Feld als Array casten
    ];

    protected $fillable = [
        'country_id',
        'country_name',
        'country_code',
        'power',
        'info',
        'typ_a',
        'typ_b',
        'typ_c',
        'typ_d',
        'typ_e',
        'typ_f',
        'typ_g',
        'typ_h',
        'typ_i',
        'typ_j',
        'typ_k',
        'typ_l',
        'typ_m',
        'typ_n',
        'plug_images',
    ];

    public function country()
    {
        return $this->belongsTo(WwdeCountry::class, 'country_id', 'id');
    }

    public function getTypAAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypBAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypCAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypDAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypEAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypFAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypGAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypHAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypIAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypJAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypKAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypLAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypMAttribute($value)
    {
        return (bool) $value;
    }

    public function getTypNAttribute($value)
    {
        return (bool) $value;
    }

}
