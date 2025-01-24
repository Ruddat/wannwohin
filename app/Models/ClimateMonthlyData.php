<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClimateMonthlyData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'climate_monthly_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id',
        'datatype',
        'value',
        'date',
        'station',
        'attributes',
    ];

    /**
     * Define the relationship to the location.
     */
    public function location()
    {
        return $this->belongsTo(WwdeLocation::class);
    }
}
