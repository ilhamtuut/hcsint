<?php
namespace App\Helpers;

class Address {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private static $link = 'https://topupclingg.com/api/location/';

    public static function province()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', self::$link.'province');
        $result = $response->getBody()->getContents();
        return json_decode($result);
    }

    public static function district($id)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', self::$link.'city/'.$id);
        $result = $response->getBody()->getContents();
        return json_decode($result);
    }

    public static function subdistrict($id)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', self::$link.'subdistrict/'.$id);
        $result = $response->getBody()->getContents();
        return json_decode($result);
    }
}
