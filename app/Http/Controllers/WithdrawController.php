<?php

namespace App\Http\Controllers;

use Response;
use App\Models\User;
use App\Models\Setting;
use App\Models\Balance;
use App\Models\Program;
use App\Models\Withdraw;
use App\Models\Question;
use App\Models\HistoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidatonFactory;

class WithdrawController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ValidatonFactory $factory)
    {
        $this->middleware('auth');

        $factory->extend(
            'multiple20',
            function ($attribute, $value, $parameters) {
                if ($value%20 == 0 ){
                    return true;
                }
            },
            'The amount must be a multiple of 20'
        );

        $factory->extend(
            'multiple50',
            function ($attribute, $value, $parameters) {
                if ($value%50 == 0 ){
                    return true;
                }
            },
            'The amount must be a multiple of 50'
        );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        if(!Auth::user()->bank){
            $request->session()->flash('failed', 'Please add your bank account first');
            return redirect()->route('user.bank');
        }
        if(!Auth::user()->question){
            $request->session()->flash('failed', 'Please enter your secret answer first');
            return redirect()->route('question.answer')->with(['link'=>route('withdraw.index')]);
        }
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Withdraw')->first()->value;
        // if($count >= 2 && $count < 6){
        //     $fee = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        // }elseif($count >= 6 && $count < 11){
        //     $fee = Setting::where('name','Fee Withdraw 6th')->first()->value;
        // }elseif($count >= 11){
        //     $fee = Setting::where('name','Fee Withdraw 11th')->first()->value;
        // }
        $kurs = Setting::where('name','Exchange Withdraw')->first()->value;
        $balance = Auth::user()->balance()->where('description', 'Cash Wallet')->first()->balance;
        $question = Question::orderBy('name')->get();
        return view('backend.withdraw.index',compact('fee','kurs','balance','question'));
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'bank'=>'required',
            'account_name'=>'required',
            'account_number'=>'required',
            'amount'=>'required|integer|multiple20|gte:20',
            'question'=>'required',
            'answer'=>'required',
            'pin_authenticator'=>'required'
        ]);

        $user = Auth::user();
        $hasPassword = Hash::check($request->answer, $user->question->answer);
        if(!$hasPassword || $user->question->question_id != $request->question){
            $attempt = $request->session()->get('count'.Auth::id());
            if(!$attempt){
                $attempt = 0;
            }
            $request->session()->put('count'.Auth::id(), ++$attempt);
            if($attempt == 3){
                $user->status = 2;
                $user->save();
            }
            $request->session()->flash('failed', 'Failed, secret answer question wrong');
            return redirect()->back();
        }

        $type_wallet = 'Cash Wallet';
        $count = Auth::user()->program()->count();
        $fee_wd = Setting::where('name','Fee Withdraw')->first()->value;
        // if($count >= 2 && $count < 6){
        //     $fee_wd = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        // }elseif($count >= 6 && $count < 11){
        //     $fee_wd = Setting::where('name','Fee Withdraw 6th')->first()->value;
        // }elseif($count >= 11){
        //     $fee_wd = Setting::where('name','Fee Withdraw 11th')->first()->value;
        // }
        $description = 'Sell '.$type_wallet.' to Bank';
        $price = Setting::where('name','Exchange Withdraw')->first()->value;
        $data_json = array(
            'bank_name' => $user->bank->bank_name,
            'account_name' => $user->bank->account_name,
            'account_number' => $user->bank->account_number
        );
        $amount = $request->amount;
        $total = $amount * $price;
        $fee = $total * $fee_wd;
        $receive = $total - $fee;
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if($hasPassword){
            $cash = $user->balance()->where('description',$type_wallet)->first();
            if($amount <= $cash->balance){
                if($this->checkDay()){
                    $checkWd = Withdraw::where([
                            'user_id'=>$user->id, 'status'=>0
                        ])->first();
                    if(is_null($checkWd)){
                        $trans = Withdraw::where(['user_id'=>Auth::user()->id])->orderBy('created_at','desc')->first();
                        if($trans){
                            $tgl = $trans->created_at;
                            $dt = date('Y-m-d H:i:s');
                            $selisih = strtotime($dt) - strtotime($tgl);
                            $minute = floor($selisih / 60);
                            if($minute >= 2){
                                $run = true;
                            }else{
                                $run = false;
                                $request->session()->flash('failed', 'Please wait to 2 minutes to do the next sell cw');
                            }
                        }else{
                            $run = true;
                        }

                        if($run){
                            Withdraw::create([
                                'user_id' => $user->id,
                                'amount' => $amount,
                                'price'=> $price,
                                'total'=> $total,
                                'fee' => $fee,
                                'receive' => $receive,
                                'status' => 0,
                                'type' => 'bank',
                                'description' => $description,
                                'json_data' => json_encode($data_json)
                            ]);

                            $cash->balance = $cash->balance - $amount;
                            $cash->save();

                            HistoryTransaction::create([
                                'balance_id' => $cash->id,
                                'from_id' => $user->id,
                                'to_id' => 1,
                                'amount' => $amount,
                                'description' => $description,
                                'status' => 1,
                                'type' => 'OUT'
                            ]);

                            // Admin
                            $balance_adm = Balance::where(['user_id'=> 1,'description'=> $type_wallet])->first();
                            $balance_adm->balance = $balance_adm->balance + $amount;
                            $balance_adm->save();

                            HistoryTransaction::create([
                                'balance_id' => $balance_adm->id,
                                'from_id' => $user->id,
                                'to_id' => 1,
                                'amount' => $amount,
                                'description' => $description.' From '.ucfirst($user->username),
                                'status' => 1,
                                'type' => 'IN'
                            ]);

                            $request->session()->flash('success', 'Successfully, '.$description);
                        }
                    }else{
                        $request->session()->flash('failed', 'Failed, Please wait for the previous sell process to complete to be able to make a sell');
                    }
                }else{
                    $request->session()->flash('failed', 'Failed, sell cw can only be made on Monday');
                }
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to sell cw');
            }
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

    public function usdt(Request $request)
    {
        if(!Auth::user()->question){
            $request->session()->flash('failed', 'Please enter your secret answer first');
            return redirect()->route('question.answer')->with(['link'=>route('withdraw.usdt')]);
        }
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Withdraw')->first()->value;
        // if($count >= 2 && $count < 6){
        //     $fee = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        // }elseif($count >= 6 && $count < 11){
        //     $fee = Setting::where('name','Fee Withdraw 6th')->first()->value;
        // }elseif($count >= 11){
        //     $fee = Setting::where('name','Fee Withdraw 11th')->first()->value;
        // }
        $kurs = Setting::where('name','Exchange Withdraw USDT')->first()->value;
        $balance = Auth::user()->balance()->where('description', 'Cash Wallet')->first()->balance;
        $question = Question::orderBy('name')->get();
        return view('backend.withdraw.wd_usdt',compact('fee','kurs','balance','question'));
    }

    public function sendUsdt(Request $request)
    {
        $this->validate($request, [
            'usdt_address'=>'required',
            'amount'=>'required|integer|multiple50|gte:50',
            'question'=>'required',
            'answer'=>'required',
            'pin_authenticator'=>'required'
        ]);

        $user = Auth::user();
        $hasPassword = Hash::check($request->answer, $user->question->answer);
        if(!$hasPassword || $user->question->question_id != $request->question){
            $attempt = $request->session()->get('count'.Auth::id());
            if(!$attempt){
                $attempt = 0;
            }
            $request->session()->put('count'.Auth::id(), ++$attempt);
            if($attempt == 3){
                $user->status = 2;
                $user->save();
            }
            $request->session()->flash('failed', 'Failed, secret answer question wrong');
            return redirect()->back();
        }

        $type_wallet = 'Cash Wallet';
        $count = $user->program()->count();
        $fee_wd = Setting::where('name','Fee Withdraw')->first()->value;
        // if($count >= 2 && $count < 6){
        //     $fee_wd = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        // }elseif($count >= 6 && $count < 11){
        //     $fee_wd = Setting::where('name','Fee Withdraw 6th')->first()->value;
        // }elseif($count >= 11){
        //     $fee_wd = Setting::where('name','Fee Withdraw 11th')->first()->value;
        // }
        $description = 'Sell '.$type_wallet.' to USDT';
        $price = Setting::where('name','Exchange Withdraw USDT')->first()->value;
        $data_json = array(
            'usdt_address' => $request->usdt_address
        );
        $amount = $request->amount;
        $total = $amount * $price;
        $fee = $total * $fee_wd;
        $receive = $total - $fee;
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if($hasPassword){
            $cash = $user->balance()->where('description',$type_wallet)->first();
            if($amount <= $cash->balance){
                if($this->checkDay()){
                    $checkWd = Withdraw::where([
                            'user_id'=>$user->id, 'status'=>0
                        ])->first();
                    if(is_null($checkWd)){
                        $trans = Withdraw::where(['user_id'=>Auth::user()->id])->orderBy('created_at','desc')->first();
                        if($trans){
                            $tgl = $trans->created_at;
                            $dt = date('Y-m-d H:i:s');
                            $selisih = strtotime($dt) - strtotime($tgl);
                            $minute = floor($selisih / 60);
                            if($minute >= 2){
                                $run = true;
                            }else{
                                $run = false;
                                $request->session()->flash('failed', 'Please wait to 2 minutes to do the next sell cw');
                            }
                        }else{
                            $run = true;
                        }

                        if($run){
                            Withdraw::create([
                                'user_id' => $user->id,
                                'amount' => $amount,
                                'price'=> $price,
                                'total'=> $total,
                                'fee' => $fee,
                                'receive' => $receive,
                                'status' => 0,
                                'type' => 'usdt',
                                'description' => $description,
                                'json_data' => json_encode($data_json)
                            ]);

                            $cash->balance = $cash->balance - $amount;
                            $cash->save();

                            HistoryTransaction::create([
                                'balance_id' => $cash->id,
                                'from_id' => $user->id,
                                'to_id' => 1,
                                'amount' => $amount,
                                'description' => $description,
                                'status' => 1,
                                'type' => 'OUT'
                            ]);

                            // Admin
                            $balance_adm = Balance::where(['user_id'=> 1,'description'=> $type_wallet])->first();
                            $balance_adm->balance = $balance_adm->balance + $amount;
                            $balance_adm->save();

                            HistoryTransaction::create([
                                'balance_id' => $balance_adm->id,
                                'from_id' => $user->id,
                                'to_id' => 1,
                                'amount' => $amount,
                                'description' => $description.' From '.ucfirst($user->username),
                                'status' => 1,
                                'type' => 'IN'
                            ]);

                            $request->session()->flash('success', 'Successfully, '.$description);
                        }
                    }else{
                        $request->session()->flash('failed', 'Failed, Please wait for the previous sell cw process to complete to be able to make a sell');
                    }
                }else{
                    $request->session()->flash('failed', 'Failed, sell cw can only be made on Monday');
                }
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to sell cw');
            }
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

    public function history(Request $request)
    {
        // $type = $request->type;
        $type = 'bank';
        $date = $request->date;
        $data = Auth::user()
                ->withdraw()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->when($type,function ($cari) use ($type) {
                    return $cari->where('type', $type);
                })
                ->orderBy('created_at','desc')
                ->paginate(20);
        $amount = Auth::user()
                ->withdraw()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->when($type,function ($cari) use ($type) {
                    return $cari->where('type', $type);
                })
                ->sum('amount');
        $total = Auth::user()
                ->withdraw()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->when($type,function ($cari) use ($type) {
                    return $cari->where('type', $type);
                })
                ->sum('total');
        $fee = Auth::user()
                ->withdraw()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->when($type,function ($cari) use ($type) {
                    return $cari->where('type', $type);
                })
                ->sum('fee');
        $receive = Auth::user()
                ->withdraw()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->when($type,function ($cari) use ($type) {
                    return $cari->where('type', $type);
                })
                ->sum('receive');
        return view('backend.withdraw.history',compact('data','date','amount','total','fee','receive'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list(Request $request, $type)
    {
        if($type != 'bank' && $type != 'usdt'){
            abort(404);
        }

        $search = $request->search;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        $status = $request->status;

        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $statusIn = [0,1,2,3,4];
        if($status == 1){
            $statusIn = [0];
        }elseif($status == 2){
            $statusIn = [1];
        }elseif($status == 3){
            $statusIn = [2];
        }
        $data = Withdraw::whereIn('status',$statusIn)
            ->where('type',$type)
            ->when($search, function ($query) use ($search,$type){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username', 'like', $search.'%');
                });
            })
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->orderBy('created_at','desc')
            ->paginate(20);

        $amount = Withdraw::whereIn('status',$statusIn)
            ->where('type',$type)
            ->when($search, function ($query) use ($search,$type){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username', 'like', $search.'%');
                });
            })
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('amount');
        $total = Withdraw::whereIn('status',$statusIn)
            ->where('type',$type)
            ->when($search, function ($query) use ($search,$type){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username', 'like', $search.'%');
                });
            })
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('total');
        $fee = Withdraw::whereIn('status',$statusIn)
            ->where('type',$type)
            ->when($search, function ($query) use ($search,$type){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username', 'like', $search.'%');
                });
            })
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('fee');
        $receive = Withdraw::whereIn('status',$statusIn)
            ->where('type',$type)
            ->when($search, function ($query) use ($search,$type){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username', 'like', $search.'%');
                });
            })
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('receive');
        return view('backend.withdraw.list',compact('data','receive','amount','type','total','fee'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function accept(Request $request, $id)
    {
        $this->validate($request, [
            'txid'=>'required',
            'pin_authenticator'=>'required'
        ]);
        $user = Auth::user();
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if($hasPassword){
            $withdraw = Withdraw::find($id);
            $json = json_decode($withdraw->json_data,true);
            $json['txid'] = $request->txid;
            $json['accepted_at'] = date('Y-m-d H:i:s');
            $withdraw->json_data = json_encode($json);
            $withdraw->status = 1;
            $withdraw->save();
            $request->session()->flash('success', 'Success, Accept Sell CW Username '.ucfirst($withdraw->user->username));
        }else{
            $request->session()->flash('failed', 'Failed, security password is wrong');
        }
        return redirect()->back();
    }

    public function reject(Request $request, $id)
    {
        $withdraw = Withdraw::find($id);
        $withdraw->status = 2;
        $withdraw->save();

        $amount = $withdraw->amount;
        // $description = $withdraw->description;
        // $x = explode("Sell ", $description);
        // $q = explode(" to Bank", $x[1]);
        // $q = explode(" to USDT", $x[1]);
        // $name_wallet = $q[0];
        $name_wallet = 'Cash Wallet';
        $balance = Balance::where(['user_id'=>1,'description'=>$name_wallet])->first();
        $balance->balance = $balance->balance - $amount;
        $balance->save();

        HistoryTransaction::create([
            'balance_id'=>$balance->id,
            'from_id'=> 1,
            'to_id'=> $withdraw->user_id,
            'amount'=> $amount,
            'description'=> 'Reject '.$withdraw->description.' Username '.ucfirst($withdraw->user->username),
            'status'=> 1,
            'type'=> 'OUT'
        ]);

        $saldo = Balance::where(['user_id'=> $withdraw->user_id,'description'=>$name_wallet])->first();
        $saldo->balance = $saldo->balance + $amount;
        $saldo->save();

        HistoryTransaction::create([
            'balance_id'=> $saldo->id,
            'from_id'=> 1,
            'to_id'=> $withdraw->user_id,
            'amount'=> $amount,
            'description'=> 'Reject '.$withdraw->description,
            'status'=> 1,
            'type'=> 'IN'
        ]);
        $request->session()->flash('success', 'Success, Reject Sell CW');
        return Response::json(['success'=>1]);
    }

    public function checkDay()
    {
        $day = date('l');
        $result = false;
        if($day == 'Monday'){
            $result = true;
        }
        return $result;
    }
}
