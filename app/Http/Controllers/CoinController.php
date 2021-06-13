<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Ico;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Balance;
use App\Models\BuyCoin;
use App\Models\Downline;
use App\Models\BonusActive;
use App\Models\LevelSponsor;
use App\Models\HistoryTransaction;
use App\Helpers\AvCoin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class CoinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $wallet = Auth::user()->wallet;
        if(!$wallet){
            $wallet = AvCoin::createWallet();
        }
        $address = $wallet->address;
        $price = AvCoin::price();
        $qrCode = AvCoin::qrcodeRender($address);
        $balance = number_format(AvCoin::balanceByAddress($address),8);
        return view('backend.avcoin.index', compact('address','qrCode','balance','price'));
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            "address" => "required|max:42",
            "amount" => "required|numeric|gte:0.00000001",
            "pin_authenticator" => "required"
        ]);

        $user = Auth::user();
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if(!$hasPassword){
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
            return redirect()->back();
        }
        $show = AvCoin::ico();
        if($show){
            $request->session()->flash('failed', 'Failed, Send AV open after ICO');
            return redirect()->back();
        }

        $wallet = $user->wallet;
        $fromAddress = $wallet->address;
        $toAddress = $request->address;
        $amount = $request->amount;

        if($fromAddress == $toAddress){
            $request->session()->flash('failed', 'Please check destination address');
            return redirect()->back();
        }

        $send = AvCoin::send($fromAddress,$toAddress,$amount);
        if($send['success']){
            $request->session()->flash('success', $send['message']);
        }else{
            $request->session()->flash('failed', $send['message']);
        }
        return redirect()->back();
    }

    public function sendFromAdmin(Request $request)
    {
        $min_buy = Ico::where('status',1)->orderBy('id','desc')->first()->min_buy;
        $this->validate($request, [
            "address" => "required|max:42",
            "amount" => "required|numeric|gte:".$min_buy,
            "pin_authenticator" => "required"
        ]);

        $user = Auth::user();
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if(!$hasPassword){
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
            return redirect()->back();
        }
        $wallet = $user->wallet;
        $fromAddress = $wallet->address;
        $toAddress = $request->address;
        $amount = $request->amount;

        if($fromAddress == $toAddress){
            $request->session()->flash('failed', 'Please check destination address');
            return redirect()->back();
        }

        $price = AvCoin::price();
        $ico = Ico::where('status',1)->orderBy('id','desc')->first();
        if($ico->rest < $amount){
            $request->session()->flash('failed', 'Rest AV coin on Price '.$price.' is '.$ico->rest);
            return redirect()->back();
        }

        $send = AvCoin::send($fromAddress,$toAddress,$amount);
        if($send['success']){
            if(Auth::user()->hasRole('admin')){
                $byUser = AvCoin::addressUser($toAddress);
                $total = $amount * $price;
                BuyCoin::create([
                    'user_id' => $byUser->id,
                    'amount' => $amount,
                    'price' => $price,
                    'total' => $total,
                    'status' => 1,
                    'description' => 'Buy AV'
                ]);
                $this->updateIco($amount);
            }
            // $this->bonusBuyAv($byUser->id,$amount,$price,$total);
            $request->session()->flash('success', $send['message']);
        }else{
            $request->session()->flash('failed', $send['message']);
        }
        return redirect()->back();
    }

    public function updateIco($amount)
    {
        $ico = Ico::where('status',1)->orderBy('id','desc')->first();
        $ico->sold = $ico->sold + $amount;
        $ico->rest = $ico->rest - $amount;
        if($ico->rest - $amount == 0){
            $ico->status = 1;
        }
        $ico->save();

        if($ico->rest == 0){
            $upIco = Ico::where('status',0)->first();
            if($upIco){
                $upIco->status = 1;
                $upIco->save();
            }
        }
    }

    public function bonusBuyAv($user_id,$amount,$price,$total)
    {
        $uplines = Downline::whereNotIn('user_id',[1,2])->where('downline_id',$user_id)->take(5)->get();
        foreach ($uplines as $key => $value) {
            $level = LevelSponsor::find(++$key);
            $percent = $level->percent;
            $bonus = $total * $percent;
            $json = array(
                'amount' => $amount,
                'price' => $price,
                'total' => $total,
            );
            if($bonus > 0){
                BonusActive::create([
                    'user_id'=>$value->user_id,
                    'from_id'=>$user_id,
                    'amount'=>$total,
                    'percent'=>$percent,
                    'bonus'=>$bonus,
                    'status'=>1,
                    'description'=>'Bonus Sponsor Buy AV '.$level->name,
                    'json'=>json_encode($json)
                ]);

                $saldoAdmin = Balance::where(['user_id'=>1,'description'=>'Selling Bonus AV'])->first();
                $saldoAdmin->balance = $saldoAdmin->balance - $bonus;
                $saldoAdmin->save();
                HistoryTransaction::create([
                    'balance_id' => $saldoAdmin->id,
                    'from_id' => 1,
                    'to_id' => $value->user_id,
                    'amount' => $bonus,
                    'description' => 'Bonus Sponsor Buy AV '.$level->name.' to '.ucfirst($value->user->username),
                    'status' => 1,
                    'type' => 'OUT'
                ]);

                $saldoUser = $value->user->balance()->where(['description'=>'Selling Bonus AV'])->first();
                $saldoUser->balance = $saldoUser->balance + $bonus;
                $saldoUser->save();

                HistoryTransaction::create([
                    'balance_id' => $saldoUser->id,
                    'from_id' => $user_id,
                    'to_id' => $value->user_id,
                    'amount' => $bonus,
                    'description' => 'Bonus Sponsor Buy AV '.$level->name,
                    'status' => 1,
                    'type' => 'IN'
                ]);
            }
        }
    }

    public function checkAddress(Request $request)
    {
        $address = $request->q;
        $data = AvCoin::addressUser($address);
        return response()->json($data);
    }

    public function explorer(Request $request)
    {
        $search = $request->search;
        $data = AvCoin::transaction($search);
        $blocks = AvCoin::blocks();
        return view('backend.avcoin.explorer',compact('data','blocks'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function block(Request $request, $block)
    {
        $data = AvCoin::transactionByBlock($block);
        return view('backend.avcoin.block',compact('data'));
    }

    public function hash(Request $request, $hash)
    {
        $data = AvCoin::hash($hash);
        return view('backend.avcoin.hash',compact('data'));
    }

    public function address(Request $request, $address)
    {
        $balance = AvCoin::balanceByAddress($address);
        $qrCode = AvCoin::qrcodeRender($address);
        $data = AvCoin::history($address);
        return view('backend.avcoin.address',compact('data','balance','qrCode','address'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function search(Request $request)
    {
        $search = $request->q;
        if($search){
            $address = AvCoin::addressValid($search);
            if($address){
                return redirect()->route('avcoin.address', $search);
            }

            $trans = AvCoin::hash($search);
            if($trans){
                return redirect()->route('avcoin.hash', $search);
            }
        }
        return redirect()->route('avcoin.explorer');
    }

    public function list(Request $request)
    {
        $search = $request->search;
        $data = Wallet::when($search, function ($query) use ($search){
                $query->whereHas('user', function ($q) use ($search){
                    $q->where('users.username', $search);
                })->orWhere('address',$search);
            })->paginate(20);
        $total = Wallet::when($search, function ($query) use ($search){
                $query->whereHas('user', function ($q) use ($search){
                    $q->where('users.username', $search);
                })->orWhere('address',$search);
            })->sum('balance');
        return view('backend.avcoin.list',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }
}
