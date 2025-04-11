<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkCoolnessVote extends Model
{
    protected $fillable = ['park_id', 'value', 'ip_address'];
}
