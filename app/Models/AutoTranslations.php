<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutoTranslations extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'original_text',
        'locale',
        'text',
    ];
}
