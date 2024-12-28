<?php

namespace App\Models;

use App\Models\WwdeLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationGallery extends Model
{
    use HasFactory;

    protected $table = 'location_galleries';
    protected $fillable = ['location_id', 'location_name', 'image_path', 'description', 'image_hash'];
   // public $timestamps = false;

   public function location()
   {
       return $this->belongsTo(WwdeLocation::class, 'location_id');
   }

}
