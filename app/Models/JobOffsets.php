<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobOffsets extends Model
{
    use HasFactory;

    protected $table = 'job_offsets';
    protected $fillable = ['job_id', 'offset'];
    public $timestamps = false;

}
