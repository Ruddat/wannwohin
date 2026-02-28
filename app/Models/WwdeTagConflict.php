<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WwdeTagConflict extends Model
{
    use HasFactory;

    protected $table = 'wwde_tag_conflicts';

    protected $fillable = [
        'raw_category',
        'suggested_slug',
        'resolved',
    ];

    protected $casts = [
        'resolved' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where('resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }
}
