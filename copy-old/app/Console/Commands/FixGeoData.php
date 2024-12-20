<?php

namespace App\Console\Commands;

use App\Library\TimezoneApiClientLibrary;
use Illuminate\Console\Command;
use  DB;
use Illuminate\Support\Facades\Schema;

class FixGeoData extends Command
{
   protected $_constants;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hani:fix_geo_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Geo Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $locations = DB::table('locations')->where('id',816)->get();
        $locations = DB::table('locations')->get();
        foreach ($locations as $location) {
            $lat = $location->lat;
            $lon = $location->lon;
            if ($lat != null and $lon != null){
                $lat_last_point = strpos($lat, '.', 5);
            $new_lat = substr_replace($lat, '', $lat_last_point, 1);

            $lon_last_point = strpos($lon, '.', 5);
            $new_lon = substr_replace($lon, '', $lon_last_point, 1);
            $time_zone_geonames = (new TimezoneApiClientLibrary())->getByPoint($new_lat, $new_lon);
            $time_zone_nearest =   (new TimezoneApiClientLibrary())->get_nearest_timezone($new_lat, $new_lon);
            if($time_zone_geonames =='') {
                $time_zone =  $time_zone_nearest;
                $source = 'get_nearest_timezone';
            }else{
                $time_zone =  $time_zone_geonames;
                $source = 'geonames.org';
            }
            $locations = DB::table('locations')->where('id', $location->id)->update(['lat_new' => $new_lat, 'lon_new' => $new_lon, 'time_zone' => $time_zone]);
           // dd();
            echo $location->title . " - time_zone: " . $time_zone . ' with: '.$source .' || zone_geonames:  '.$time_zone_geonames .' , nearest_timezone:  ' .$time_zone_nearest   . PHP_EOL;
            }
        }
   }


}

