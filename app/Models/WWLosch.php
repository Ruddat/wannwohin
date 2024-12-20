<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WWLosch extends Model
{
    use HasFactory;

    protected $table = 'w_w_losches';
    protected $primaryKey = 'id';
    protected $fillable = ['Name', 'BSP', 'EW', 'Preis'];
    public $timestamps = true;

}
