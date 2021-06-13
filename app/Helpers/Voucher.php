<?php
namespace App\Helpers;

class Voucher {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private static $nameApp = 'Hcs_com';
    private static $api_token = 'jdgbhldiufhe89385nwe7y394tes';
    private static $link = 'https://loki.harmonyb12.com/api_ppob/index.php/ptiga/controller_data/';
    private static $link_trans = 'https://loki.harmonyb12.com/api_ppob/index.php/ptiga/controller_transaksi/';

    public static function list()
    {
        if(env("APP_ENV") == 'production') {
            self::$nameApp = 'Hcs_com';
            self::$api_token = 'jdgbhldiufhe89385nwe7y394';
            self::$link = 'http://103.226.51.74/index.php/ptiga/controller_data/';
            self::$link_trans = 'http://103.226.51.74/index.php/ptiga/controller_transaksi/';
        }
        $jenis = 'online';
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', self::$link.'paket_voucher', [
            'form_params' => [
                'key' => self::$api_token,
                'nama' => self::$nameApp,
                'jenis' => $jenis
            ]
        ]);
        $result = $response->getBody()->getContents();
        return json_decode($result)->catatan;
    }

    public static function bayar_voucher($id_paket)
    {
        if(env("APP_ENV") == 'production') {
            self::$nameApp = 'Hcs_com';
            self::$api_token = 'jdgbhldiufhe89385nwe7y394';
            self::$link = 'http://103.226.51.74/index.php/ptiga/controller_data/';
            self::$link_trans = 'http://103.226.51.74/index.php/ptiga/controller_transaksi/';
        }
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', self::$link_trans.'buy_voucher', [
            'form_params' => [
                'key' => self::$api_token,
                'nama' => self::$nameApp,
                'id_paket' => $id_paket
            ]
        ]);
        $result = $response->getBody()->getContents();
        return json_decode($result);
    }

    public static function data_voucher($id_paket)
    {
        if(env("APP_ENV") == 'production') {
            self::$nameApp = 'Hcs_com';
            self::$api_token = 'jdgbhldiufhe89385nwe7y394';
            self::$link = 'http://103.226.51.74/index.php/ptiga/controller_data/';
            self::$link_trans = 'http://103.226.51.74/index.php/ptiga/controller_transaksi/';
        }
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', self::$link.'view_kode', [
            'form_params' => [
                'key' => self::$api_token,
                'nama' => self::$nameApp,
                'id_paket' => $id_paket
            ]
        ]);
        $result = $response->getBody()->getContents();
        return json_decode($result)->catatan;
    }
}
