<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModStaticPage extends Model
{
    protected $fillable = ['slug', 'title', 'body'];

    // Optional: Slug als Primärschlüssel verwenden
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'slug';
}
