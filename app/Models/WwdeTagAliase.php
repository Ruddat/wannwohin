<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WwdeTagAliase extends Model
{
    use HasFactory;

    protected $table = 'wwde_tag_aliases';

    protected $fillable = [
        'tag_id',
        'alias',
        'alias_slug',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function tag()
    {
        return $this->belongsTo(WwdeTag::class, 'tag_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($alias) {
            if (!$alias->alias_slug) {
                $alias->alias_slug = Str::slug($alias->alias);
            }
        });
    }

public function parent()
{
    return $this->belongsTo(WwdeTag::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(WwdeTag::class, 'parent_id');
}


}
