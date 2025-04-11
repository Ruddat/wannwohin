<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModTrip extends Model
{
    use HasFactory;

    protected $table = 'mod_trips';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'main_location',
        'days',
        'use_days',
        'is_public',
        'views',
        'clicks',
    ];

    protected $casts = [
        'days' => 'array',
        'use_days' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
