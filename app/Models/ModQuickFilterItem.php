<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModQuickFilterItem extends Model
{
    use HasFactory;

    protected $table = 'mod_quick_filter_items';

    protected $fillable = [
        'title',
        'slug',
        'title_text',
        'content',
        'thumbnail',
        'panorama',
        'image',
        'filter_months',
        'status',
        'sort_order',
    ];

    // If you want to automatically cast filter_months to array
    protected $casts = [
        'filter_months' => 'array',
    ];
}
