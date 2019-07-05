<?php

namespace Framework\lib;


class Geolocation
{
    private $ipAddress;
    private $api;
    private $fields;    // refer to http://ip-api.com/docs/api:returned_values#field_generator

    // on success all except message
    // on fail ip/status/message
    public $status;
    public $query;
    public $country;
    public $countryCode;
    public $region;
    public $regionName;
    public $city;
    public $zip;
    public $lat;
    public $lon;
    public $timezone;
    public $isp;
    public $org;
    public $as;
    public $message;

    public function __construct()
    {
        $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->api = "http://ip-api.com/php/";
        $this->fields= 65535;
    }

    public function Initialize()
    {
        $data = $this->Communicate($this->ipAddress);
        if ($data) {
            if ($data && $data['status'] !== 'success') {
                $this->status = $data['status'];
                $this->query = $data['query'];
                $this->message = $data['message'];
            } else {
                foreach ($data as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    private function Communicate($query)
    {
        if ($query !== null) {
            $url = $this->api . $query . '?fields=' . $this->fields;
            if(is_callable('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $result = unserialize(curl_exec($ch));
                curl_close($ch);
            } else {
                $result = unserialize(file_get_contents($url));
            }
            return $result;
        }
    }

    public static function vincentyCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000
    ) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}