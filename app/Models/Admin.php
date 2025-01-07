<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    // Fülleigenschaften für Massenbearbeitung
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Verborgene Felder in JSON-Ausgaben
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
