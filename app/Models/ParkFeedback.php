<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkFeedback extends Model
{
    protected $fillable = ['park_id', 'rating', 'comment'];

}
