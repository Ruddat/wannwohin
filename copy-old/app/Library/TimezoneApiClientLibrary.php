<?php

namespace App\Library;

//use Http;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class TimezoneApiClientLibrary {

        public $api_main_url ;
        public $api_key ;

    public function __construct()
    {
        $this->api_main_url = config('custom.timeZoneApi.apiurl');
        $this->api_username = config('custom.timeZoneApi.username');
//        $this->api_key = config('app.weather.appid');
    }

    //Gets the current time of a time zone.
    public function GetCurrentTimeByTimeZone($time_zone = 'Europe/Berlin'){
        $response = Http::get($this->api_main_url.'Time/current/zone?timeZone='.$time_zone);
        //$data = $response->json('time', '');
        return $response;
    }
    function getTimeZoneDirect($latitude, $longitude, $username='hmasoud' ){
// Library laravel        https://github.com/michaeldrennen/Geonames
//        https://inkplant.com/code/get-timezone
        $response = Http::get('http://api.geonames.org/timezoneJSON?lat='.$latitude.'&lng='.$longitude.'&username='.$username);
        $data = $response->json('timezoneId', '');
        return $data;
    }
    public function get($resource, array $parameters)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $this->api_key,
        ];
//        $response = Http::withHeaders($headers)->get($this->api_main_url.$resource, $parameters);
        $response = Http::withHeaders($headers)->get($this->api_main_url.$resource, $parameters);
        $data = $response->json('', '');
        return $data;

//        $response = Http::withHeaders($headers)->get($apiURL, [
//            'id' => $id,
//            'some_another_parameter' => $param
//        ]);
    }

//$nowInLondonTz = Carbon::now(new DateTimeZone('Europe/London'));
//https://www.php.net/manual/en/timezones.php
    function getByPoint($lat, $lon){
//        $url = $this->api_main_url.'timezoneJSON?lat='.$lat.'&lng='.$lon.'&username='.$this->api_username;
//////        $result = get_url_content($url);
//        $ch = curl_init($url);
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
//
//        $content = curl_exec( $ch );
//        $err     = curl_errno( $ch );
//        $errmsg  = curl_error( $ch );
//        $header  = curl_getinfo( $ch );
//        curl_close( $ch );
//
//        $header['errno']   = $err;
//        $header['errmsg']  = $errmsg;
//        $header['content'] = $content;
////
//        dd($header);
//        return $header;

        //timezone for one NY co-ordinate
//       return $this->get_nearest_timezone($lat,$lon);
       return $this->getTimeZoneDirect($lat,$lon,  'hmasoud');
//       return $this->get_timezone($lat,$lon,  'hmasoud');
//       return $this->get_timezone(-33.8578101, 151.2138672, 'hmasoud');
//        dd( $this->get_nearest_timezone(30.118867,31.402527) );
//        dd( $this->get_nearest_timezone('2.845193','-13.869951') );
// more faster and accurate if you can pass the country code
        //echo get_nearest_timezone(40.772222, -74.164581, 'US') ;
//   dd($this->getTimeZoneList());
    }

    function getTimeZoneList()
    {
        return \Cache::rememberForever('timezones_list_collection', function () {
            $timestamp = time();
            foreach (timezone_identifiers_list(\DateTimeZone::ALL) as $key => $value) {
                date_default_timezone_set($value);
                $timezone[$value] = $value . ' (UTC ' . date('P', $timestamp) . ')';
            }
            return collect($timezone)->sortKeys();
        });
    }


    function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
        $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
            : DateTimeZone::listIdentifiers();

        if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = '';
            $tz_distance = 0;

            //only one identifier?
            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach($timezone_ids as $timezone_id) {
                    $timezone = new DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat   = $location['latitude'];
                    $tz_long  = $location['longitude'];

                    $theta    = $cur_long - $tz_long;
                    $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                        + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));
                    // echo '<br />'.$timezone_id.' '.$distance;

                    if (!$time_zone || $tz_distance > $distance) {
                        $time_zone   = $timezone_id;
                        $tz_distance = $distance;
                    }

                }
            }
            return  $time_zone;
        }
        return 'unknown';
    }

    function get_timezone($latitude, $longitude, $username='hmasoud') {

        // error checking
        if (!is_numeric($latitude)) { return get_timezone_error('A numeric latitude is required.'); }
        if (!is_numeric($longitude)) { return get_timezone_error('A numeric longitude is required.'); }
        if (!$username) { return get_timezone_error('A GeoNames user account is required. You can get one here: http://www.geonames.org/login'); }

        // connect to web service
        $url = 'http://api.geonames.org/timezoneJSON?lat='.$latitude.'&lng='.$longitude.'&username='.$username;
//        $url = 'http://api.geonames.org/timezoneJSON?lat=47.01&lng=10.2&username=hmasoud';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        curl_close($ch);
        if (!$xml) { return get_timezone_error('The GeoNames service did not return any data: '.$url); }
return $xml;
        // parse XML response
        $data = new SimpleXMLElement($xml);
        $timezone = trim(strip_tags($data->timezone->timezoneId));
        if ($timezone) { return $timezone; }
        else { return 'The GeoNames service did not return a time zone: '.$url; }

    }


    function get_timezone2($latitude, $longitude, $username='hmasoud') {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);

        $executionStartTime = microtime(true);

        $url='http://api.geonames.org/timezoneJSON?lat=' . $latitude . '&lng=' . $longitude . '&username='.$username;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);

        $result=curl_exec($ch);

        curl_close($ch);

        $decode = json_decode($result,true);

        $output['status']['code'] = "200";
        $output['status']['name'] = "ok";
        $output['status']['description'] = "success";
        $output['status']['returnedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
        $output['data'] = $decode;

        header('Content-Type: application/json; charset=UTF-8');

        return $result;

    }

    function get_timezone_error($error_text) {
        echo '<div class="alert alert-danger">Error: '.$error_text.'</div>';
        return false;
    }

//    const METHODE_GET    = 'GET';
//    const METHODE_PUT    = 'PUT';
//    const METHODE_POST   = 'POST';
//    const METHODE_DELETE = 'DELETE';
//    protected $validMethods = array(
//        self::METHODE_GET,
//        self::METHODE_PUT,
//        self::METHODE_POST,
//        self::METHODE_DELETE
//    );
//    protected $apiUrl;
//    protected $cURL;
//    public $msg;
//    public $ack = 1;
//    protected $_return_type = '';
//    /**
//     * @var string[]
//     */
//    private $header;
//
//
//    public function __construct() {
//        $apiUrl = config('infosys.api.freshdesk.ApiSettings.apiUrl');
//        $this->apiUrl = rtrim($apiUrl, '/') . '/';
//        $apiKey =  config('infosys.api.freshdesk.ApiSettings.apiKey');
//        //Initializes the cURL instance
//        $this->cURL = curl_init();
//        $this->header = array(
//            'Content-Type: application/json; charset=utf-8',
//            'Authorization:' . base64_encode($apiKey),
//        );
//        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
//        //curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
//        //curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        // curl_setopt($this->cURL, CURLOPT_USERPWD, $apiUsername . ':' . $apiKey);
//        curl_setopt($this->cURL, CURLOPT_HTTPHEADER,   $this->header);
//    }
//
//    public function call($url, $method = self::METHODE_GET, $data = array(), $params = array()) {
//        if (!in_array($method, $this->validMethods)) {
//            throw new Exception('Invalid HTTP-Methode: ' . $method);
//        }
//        $queryString = '';
//        if (!empty($params)) {
//            $queryString = '&'.http_build_query($params);
//        }
//        $url = rtrim($url, '?') . '?';
//        // $apiKey =  config('custom.freshsalesApiSettings.apiKey');
//        $url = $this->apiUrl . $url ;
//        $dataString =   json_encode($data);
//        //set_time_limit(30);
//        curl_setopt($this->cURL, CURLOPT_URL, $url);
//        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
//        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
//        $result   = curl_exec($this->cURL);
//        $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);
//        // return $this->prepareResponse($result, $httpCode);
//        return ($this->_return_type == 'array') ? ['result'=> json_decode( $result, true),'httpCode' => $httpCode ]: $result ;
//    }
//
//    public function get($url, $params = array(), $return_type ='josn') {
//        $this->_return_type = $return_type;
////        $this->header[] = $data;
//        return $this->call($url, self::METHODE_GET, array(), $params);
//    }
//
//    public function post($url, $data = array(), $params = array(), $return_type ='array') {
//        $this->_return_type = $return_type;
//        $this->header[] = $data;
//        return $this->call($url, self::METHODE_POST, $data, $params);
//    }
//
//    public function put($url, $data = array(), $params = array(), $return_type ='array') {
//        $this->_return_type = $return_type;
//        $this->header[] = $data;
//        return $this->call($url, self::METHODE_PUT, $data, $params);
//    }
//
//    public function delete($url, $params = array()) {
//        return $this->call($url, self::METHODE_DELETE, array(), $params);
//    }

}

