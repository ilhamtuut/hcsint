<?php
namespace App\Helpers;

use DB;
use Auth;
use App\Models\Ico;
use App\Models\Wallet;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Models\WalletTransaction;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AvCoin {

    public static function createWallet()
    {
        $address = Str::random(42);
        $key = '0x'.Auth::id().'AV'.$address;
        $secret_key = hash('sha256', $key);
        return Wallet::create([
            'user_id' => Auth::id(),
            'address' => $address,
            'secret_key' => $secret_key,
            'balance' => 0,
            'currency' => 'AV',
            'status' => 1
        ]);
    }

    public static function balance()
    {
        $balance = 0;
        $wallet = Auth::user()->wallet;
        if($wallet){
            $balance = $wallet->balance;
        }
        return $balance;
    }

    public static function balanceByAddress($address)
    {
        $balance = 0;
        $wallet = Wallet::where('address',$address)->first();
        if($wallet){
            $balance = $wallet->balance;
        }
        return $balance;
    }

    public static function addressValid($address)
    {
        $result = false;
        $wallet = Wallet::where('address',$address)->first();
        if($wallet){
            $result = true;
        }
        return $result;
    }

    public static function addressUser($address)
    {
        $result = null;
        $wallet = Wallet::where('address',$address)->first();
        if($wallet){
            $result = $wallet->user;
        }
        return $result;
    }

    public static function send($fromAddress,$toAddress,$amount)
    {
        $result = array('success'=>false,'message' => 'Please try again.');
        $from = Wallet::where('address',$fromAddress)->first();
        $to = Wallet::where('address',$toAddress)->first();
        if($from && $amount > 0 && $from->balance < $amount){
            return $result = array('success'=>false,'message'=>'Balance not enough');
        }

        if(!$to){
            return $result = array('success'=>false,'message'=>'Address not valid');
        }

        $from->balance = $from->balance - $amount;
        $from->save();

        $to->balance = $to->balance + $amount;
        $to->save();

        $lastblock = WalletTransaction::lastBlock();
        $data = array(
            'block' =>$lastblock,
            'from_address' =>$fromAddress,
            'to_address' =>$toAddress,
            'amount' =>$amount,
        );
        $hash = hash('sha256', json_encode($data));
        WalletTransaction::create([
            'hash' =>$hash,
            'from_address' =>$fromAddress,
            'to_address' =>$toAddress,
            'amount' =>$amount,
            'status' =>1
        ]);

        $result = array('success'=>true,'message'=>'Success Send Coin AV');
        return $result;
    }

    public static function hash($hash)
    {
        return WalletTransaction::where('hash',$hash)->first();
    }

    public static function history($address)
    {
        return WalletTransaction::where('from_address',$address)
                ->orWhere('to_address',$address)
                ->orderBy('id','desc')
                ->paginate(20);
    }

    public static function transaction($search)
    {
        return WalletTransaction::when($search, function ($query) use ($search){
                    $query->where('hash', $search)
                        ->orWhere('from_address', $search)
                        ->orWhere('to_address', $search);
                })
                ->orderBy('id','desc')
                ->paginate(20);
    }

    public static function blocks()
    {
        return WalletTransaction::select('block', DB::raw('count(*) as txn'), DB::raw('sum(amount) as av'))
                ->groupBy('block')
                ->orderBy('block','desc')
                ->paginate(10);
    }

    public static function transactionByBlock($block)
    {
        return WalletTransaction::where('block', $block)
                ->orderBy('id','desc')
                ->paginate(20);
    }

    public static function qrcode($address)
    {
        $client = new \GuzzleHttp\Client();
        $endPoint = 'https://mydinasty.live/qrcode/'.$address;
        $promise = $client->getAsync($endPoint)->then(
            function ($response) {
                return json_decode($response->getBody(), true);
            }, function ($exception) {
                return false;
            }
        );
        $qrCode = $promise->wait()['qrcode'];
        return $qrCode;
    }

    public static function qrcodeRender($address)
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(250),
                new ImagickImageBackEnd()
            )
        );
        $qrCode = 'data:image/png;base64,'.base64_encode($writer->writeString($address));
        return $qrCode;
    }

    public static function price()
    {
        $price = Ico::where('status',1)->orderBy('id','desc')->first()->price;;
        $totalSupply = Ico::sum('amount');
        $sold = Ico::sum('sold');
        if($totalSupply == $sold){
            $price = Setting::where('name','Price AV After ICO')->first()->value;
        }
        return $price;
    }

    public static function ico()
    {
        $ico = false;
        $datenow = date('Y-m-d');
        $date = env('SET_DATE');
        $set_date = date('Y-m-d',strtotime('+100 day', strtotime($date)));
        $totalSupply = Ico::sum('amount');
        $sold = Ico::sum('sold');
        if($datenow <= $set_date || $totalSupply == $sold){
            $ico = true;
        }
        return $ico;
    }
}
