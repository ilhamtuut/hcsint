<?php
namespace App\Helpers;

use GuzzleHttp\Middleware;

class Convert {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // private static
    private static $link = 'http://topup.test/api/hcs/';

    public static function send($from,$username,$amount)
    {
        if(env("APP_ENV") == 'production') {
            self::$link = 'https://topupclingg.com/api/hcs/';
        }

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->request('POST', self::$link.'send',[
                    'auth' => [
                        'my-topup',
                        'UhLk6sX9pY0le1'
                    ],
                    'json' => [
                        'from' => $from,
                        'username' => $username,
                        'amount' => $amount
                    ]
                ]);
            $result = $response->getBody()->getContents();
        }catch(\GuzzleHttp\Exception\ClientException $error){
            $response = $error->getResponse();
            $result = $response->getBody()->getContents();
        }
        return json_decode($result);
    }

    public static function check($username)
    {
        if(env("APP_ENV") == 'production') {
            self::$link = 'https://topupclingg.com/api/hcs/';
        }

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->request('GET', self::$link.'check/'.$username,[
                    'auth' => [
                        'my-topup',
                        'UhLk6sX9pY0le1'
                    ]
                ]);
            $result = $response->getBody()->getContents();
        }catch(\GuzzleHttp\Exception\ClientException $error){
            $response = $error->getResponse();
            $result = $response->getBody()->getContents();
        }
        return json_decode($result);
    }
}
