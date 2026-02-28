<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    protected $table = 'wwde_category_mappings';

    protected $fillable = [
        'raw_category',
        'tag_id',
    ];

    public function tag()
    {
        return $this->belongsTo(WwdeTag::class, 'tag_id');
    }
}
