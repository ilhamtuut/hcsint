<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Helpers\AvCoin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'=> 'required',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->passes()){
            $user = User::where('username',$request->username)->first();
            if($user){
                $hasPassword = Hash::check($request->password, $user->password);
                if($hasPassword){
                    $user->api_token = Str::random(60);
                    $user->save();
                    $data = array(
                        'success' => true,
                        'message'=>'Successfully',
                        'data'=> $user
                    );
                }else{
                    $data = array(
                        'success' => false,
                        'message'=>'Password is wrong'
                    );
                }
            }else{
                $data = array(
                    'success' => false,
                    'message'=>'Username not found'
                );
            }
        }else{
            $data = array(
                'success' => false,
                'message' => 'Field is still empty',
                'errors' => $validator->errors()
            );
        }
        return response()->json($data);
    }

    public function wallet(Request $request)
    {
        $wallet = Auth::user()->wallet;
        if(!$wallet){
            $wallet = AvCoin::createWallet();
        }
        $address = $wallet->address;
        $balance = number_format(AvCoin::balanceByAddress($address),8);
        $data = array(
            'success'=>true,
            'data'=> [
                'address' => $address,
                'balance' => $balance
            ]
        );
        return response()->json($data);
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "address" => "required|max:42",
            "amount" => "required|numeric|gte:0.00000001",
            "pin_authenticator" => "required"
        ]);

        if (!$validator->passes()){
            $data = array(
                'success' => false,
                'message' => 'Field is still empty',
                'errors' => $validator->errors()
            );
            return response()->json($data);
        }

        $user = Auth::user();
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if(!$hasPassword){
            $data = array(
                'success'=>false,
                'message'=> 'Failed, PIN Authenticator is wrong'
            );
            return response()->json($data);
        }
        $show = AvCoin::ico();
        if($show){
            $data = array(
                'success'=>false,
                'message'=> 'Failed, Send AV open after ICO'
            );
            return response()->json($data);
        }

        $wallet = $user->wallet;
        $fromAddress = $wallet->address;
        $toAddress = $request->address;
        $amount = $request->amount;

        if($fromAddress == $toAddress){
            $data = array(
                'success'=>false,
                'message'=> 'Please check destination address'
            );
            return response()->json($data);
        }

        $send = AvCoin::send($fromAddress,$toAddress,$amount);
        if($send['success']){
            $data = array(
                'success'=>true,
                'message'=> $send['message']
            );
            return response()->json($data);
        }else{
            $data = array(
                'success'=>false,
                'message'=> $send['message']
            );
            return response()->json($data);
        }
    }

    public function history(Request $request)
    {
        $wallet = Auth::user()->wallet;
        if(!$wallet){
            $data = array(
                'success'=>false,
                'message'=> 'Dont have address'
            );
            return response()->json($data);
        }
        $address = $wallet->address;
        $history = AvCoin::history($address);
        $data = array(
            'success'=>true,
            'data'=> $history
        );
        return response()->json($data);
    }
}
