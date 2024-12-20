<?php

namespace App\Library;

//use Http;
use Illuminate\Support\Facades\Http;

class TimeApiClientLibrary {

        public $api_main_url ;
        public $api_key ;

    public function __construct()
    {
        $this->api_main_url = config('app.time.apiUrl');
//        $this->api_key = config('app.weather.appid');
    }

    //Gets the current time of a time zone.
    public function GetCurrentTimeByTimeZone($time_zone = 'Europe/Berlin'){
        $response = Http::get($this->api_main_url.'Time/current/zone?timeZone='.$time_zone);
        //$data = $response->json('time', '');
        return $response;
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

    function getTimeZone (){
        $url = $this->config['apiurl'].'timezoneJSON?lat='.$lat.'&lng='.$lon.'&username='.$this->config['username'];

        $result = get_url_content($url);

        return $result;
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

