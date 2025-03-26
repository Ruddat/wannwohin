<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeaderContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bg_img',
        'main_img',
        'main_text',
        'title',
        'slug', // Slug bleibt fillable
    ];
}
