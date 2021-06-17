<?php

namespace App\Http\Controllers;


use DB;
use Mail;
use Response;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Balance;
use App\Models\Convert;
use App\Models\Program;
use App\Models\Question;
use App\Helpers\AvCoin;
use App\Helpers\Convert as TopUp;
use App\Helpers\Voucher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HistoryTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidatonFactory;

class ConvertController extends Controller
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
            'multiple10',
            function ($attribute, $value, $parameters) {
                if ($value%10 == 0 ){
                    return true;
                }
            },
            'The amount must be a multiple of 10'
        );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        if(!Auth::user()->question){
            $request->session()->flash('failed', 'Please enter your secret answer first');
            return redirect()->route('question.answer')->with(['link'=>route('convert.index')]);
        }
        $price = AvCoin::price();
        $exchange = Setting::where('name','Exchange Convert')->first()->value;
        $fee = Setting::where('name','Fee Convert')->first()->value;
        $additional = Setting::where('name','Additional Convert')->first()->value;
        $balance = number_format(Auth::user()->balance()->where('description','Cash Wallet')->first()->balance,2);
        $question = Question::orderBy('name')->get();
        return view('backend.convert.index',compact('balance','price','exchange','fee','question','additional'));
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'amount'=>'required|integer|gte:10|multiple10',
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
        $amount = $request->amount;
        $exchange = Setting::where('name','Exchange Convert')->first()->value;
        $fee = Setting::where('name','Fee Convert')->first()->value;
        $additional = Setting::where('name','Additional Convert')->first()->value;
        $add_amount = $amount * $additional;
        $total =  $amount * $exchange;
    	$amountFee = $total * $fee;
    	$receive = ($total - $amountFee) + $add_amount;
        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            $type_wallet = 'Cash Wallet';
            $to_wallet = 'Register Wallet';
            $description = 'Convert '.$type_wallet.' to '.$to_wallet;
            $user = Auth::user();
            $cash = $user->balance()->where('description',$type_wallet)->first();
            $to_balance = $user->balance()->where('description',$to_wallet)->first();
            if($amount > 0 && $amount <= $cash->balance){
                Convert::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'price'=> $exchange,
                    'fee'=> $amountFee,
                    'additional' => $add_amount,
                    'receive' => $receive,
                    'total' => $total,
                    'type' => 'usd',
                    'status' => 1,
                    'description' => $description
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
                    'description' => $description.' from '.ucfirst($user->username),
                    'status' => 1,
                    'type' => 'IN'
                ]);

                // to balance
                // Admin
                $balance_adm = Balance::where(['user_id'=> 1,'description'=> $to_wallet])->first();
                $balance_adm->balance = $balance_adm->balance - $receive;
                $balance_adm->save();

                HistoryTransaction::create([
                    'balance_id' => $balance_adm->id,
                    'from_id' => $user->id,
                    'to_id' => 1,
                    'amount' => $receive,
                    'description' => $description.' from '.ucfirst($user->username),
                    'status' => 1,
                    'type' => 'OUT'
                ]);

                $to_balance->balance = $to_balance->balance + $receive;
                $to_balance->save();

                HistoryTransaction::create([
                    'balance_id' => $to_balance->id,
                    'from_id' => $user->id,
                    'to_id' => 1,
                    'amount' => $receive,
                    'description' => $description,
                    'status' => 1,
                    'type' => 'IN'
                ]);
                $request->session()->flash('success', 'Successfully, '.$description);
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to convert');
            }
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function voucher(Request $request)
    {
        if(!Auth::user()->question){
            $request->session()->flash('failed', 'Please enter your secret answer first');
            return redirect()->route('question.answer')->with(['link'=>route('convert.voucher')]);
        }
        $vouchers = Voucher::list();
        $exchange = Setting::where('name','Exchange Convert Voucher')->first()->value;
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Convert Voucher')->first()->value;
        if($count >= 2 && $count < 6){
            $fee = Setting::where('name','Fee Convert Voucher 2nd')->first()->value;
        }elseif($count >= 6 && $count < 11){
            $fee = Setting::where('name','Fee Convert Voucher 6th')->first()->value;
        }elseif($count >= 11){
            $fee = Setting::where('name','Fee Convert Voucher 11th')->first()->value;
        }
        $balance = number_format(Auth::user()->balance()->where('description','Cash Wallet')->first()->balance,2);
        $question = Question::orderBy('name')->get();
        return view('backend.convert.voucher',compact('balance','exchange','vouchers','fee','question'));
    }

    public function sendVoucher(Request $request)
    {
        $this->validate($request, [
            'voucher'=>'required',
            'voucher_name'=>'required',
            'price'=>'required',
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

        $voucherId = $request->voucher;
        $voucher_name = $request->voucher_name;
        $price = $request->price;
        $exchange = Setting::where('name','Exchange Convert Voucher')->first()->value;
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Convert Voucher')->first()->value;
        if($count >= 2 && $count < 6){
            $fee = Setting::where('name','Fee Convert Voucher 2nd')->first()->value;
        }elseif($count >= 6 && $count < 11){
            $fee = Setting::where('name','Fee Convert Voucher 6th')->first()->value;
        }elseif($count >= 11){
            $fee = Setting::where('name','Fee Convert Voucher 11th')->first()->value;
        }
        $amount =  round(($price / $exchange) + 0.01,2);
        $amountFee = round($amount * $fee,2);
        $total = round($amount + $amountFee,2);
        $user = Auth::user();
        $hasPassword = Hash::check($request->pin_authenticator, $user->trx_password);
        if($hasPassword){
            $type_wallet = 'Cash Wallet';
            $description = 'Convert '.$type_wallet.' to Voucher '.$voucher_name;
            $cash = $user->balance()->where('description',$type_wallet)->first();
            if($total > 0 && $total <= $cash->balance){
                $pay = Voucher::bayar_voucher($voucherId);
                Log::info('Log payment voucher.', ['data' => $pay]);
                if(!is_null($pay) && $pay->status){
                    $pay->price = $price;
                    Convert::create([
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'price'=> $exchange,
                        'fee'=> $amountFee,
                        'receive' => 0,
                        'total' => $total,
                        'type' => 'voucher',
                        'status' => 1,
                        'description' => $description,
                        'json' => json_encode($pay)
                    ]);

                    $cash->balance = $cash->balance - $total;
                    $cash->save();

                    HistoryTransaction::create([
                        'balance_id' => $cash->id,
                        'from_id' => $user->id,
                        'to_id' => 1,
                        'amount' => $total,
                        'description' => $description,
                        'status' => 1,
                        'type' => 'OUT'
                    ]);

                    // Admin
                    $balance_adm = Balance::where(['user_id'=> 1,'description'=> $type_wallet])->first();
                    $balance_adm->balance = $balance_adm->balance + $total;
                    $balance_adm->save();

                    HistoryTransaction::create([
                        'balance_id' => $balance_adm->id,
                        'from_id' => $user->id,
                        'to_id' => 1,
                        'amount' => $total,
                        'description' => $description.' from '.ucfirst($user->username),
                        'status' => 1,
                        'type' => 'IN'
                    ]);

                    $request->session()->flash('success', 'Successfully, '.$description);
                }else{
                    $request->session()->flash('failed', 'Failed to convert');
                }
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to convert');
            }
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function history(Request $request)
    {
        $date = $request->date;
        $data = Auth::user()
                ->convert()
                ->where('type','usd')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->orderBy('created_at','desc')
                ->paginate(20);
        $total = Auth::user()
                ->convert()
                ->where('type','usd')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum('amount');
        $receive = Auth::user()
                ->convert()
                ->where('type','usd')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum(DB::raw('receive'));
        return view('backend.convert.history',compact('data','total','receive'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function history_voucher(Request $request)
    {
        $date = $request->date;
        $data = Auth::user()
                ->convert()
                ->where('type','voucher')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->orderBy('created_at','desc')
                ->paginate(20);
        $total = Auth::user()
                ->convert()
                ->where('type','voucher')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum('total');
        return view('backend.convert.history_voucher',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list(Request $request)
    {
        $search = $request->search;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        $choose = $request->choose;

        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $status = [0,1,2,3,4];
        $data = Convert::whereIn('status',$status)
                ->where('type','usd')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->orderBy('created_at','desc')
                ->paginate(20);

        $total = Convert::whereIn('status',$status)
                ->where('type','usd')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('amount');

        $receive = Convert::whereIn('status',$status)
                ->where('type','usd')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('receive');
        return view('backend.convert.list',compact('data','receive','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_voucher(Request $request)
    {
        $search = $request->search;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        $choose = $request->choose;

        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $status = [0,1,2,3,4];
        $data = Convert::whereIn('status',$status)
                ->where('type','voucher')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->orderBy('created_at','desc')
                ->paginate(20);

        $total = Convert::whereIn('status',$status)
                ->where('type','voucher')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('amount');
        return view('backend.convert.list_voucher',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function topup(Request $request)
    {
        if(!Auth::user()->question){
            $request->session()->flash('failed', 'Please enter your secret answer first');
            return redirect()->route('question.answer')->with(['link'=>route('convert.voucher')]);
        }
        $exchange = Setting::where('name','Exchange Withdraw')->first()->value;
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Withdraw')->first()->value;
        if($count >= 2 && $count < 6){
            $fee = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        }elseif($count >= 6 && $count < 11){
            $fee = Setting::where('name','Fee Withdraw 6th')->first()->value;
        }elseif($count >= 11){
            $fee = Setting::where('name','Fee Withdraw 11th')->first()->value;
        }
        $balance = number_format(Auth::user()->balance()->where('description','Cash Wallet')->first()->balance,2);
        $question = Question::orderBy('name')->get();
        return view('backend.convert.topup',compact('balance','exchange','fee','question'));
    }

    public function sendTopup(Request $request)
    {
        $this->validate($request, [
            'account'=>'required',
            'amount'=>'required|numeric|gte:10',
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

        $account = $request->account;
        $amount = $request->amount;
        $exchange = Setting::where('name','Exchange Withdraw')->first()->value;
        $count = Auth::user()->program()->count();
        $fee = Setting::where('name','Fee Withdraw')->first()->value;
        if($count >= 2 && $count < 6){
            $fee = Setting::where('name','Fee Withdraw 2nd')->first()->value;
        }elseif($count >= 6 && $count < 11){
            $fee = Setting::where('name','Fee Withdraw 6th')->first()->value;
        }elseif($count >= 11){
            $fee = Setting::where('name','Fee Withdraw 11th')->first()->value;
        }
        $total =  $amount * $exchange;
    	$amountFee = $total * $fee;
    	$receive = $total - $amountFee;
        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            $type_wallet = 'Cash Wallet';
            $description = 'Convert '.$type_wallet.' to TopupClingg';
            $user = Auth::user();
            $cash = $user->balance()->where('description',$type_wallet)->first();
            if($amount > 0 && $amount <= $cash->balance){
                $send = Topup::send($user->username,$account,$receive);
                if($send->success){
                    $json = array('account'=>$account);
                    Convert::create([
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'price'=> $exchange,
                        'fee'=> $fee,
                        'receive' => $receive,
                        'total' => $total,
                        'type' => 'topupcling',
                        'status' => 1,
                        'description' => $description,
                        'json' => json_encode($json)
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
                        'description' => $description.' from '.ucfirst($user->username),
                        'status' => 1,
                        'type' => 'IN'
                    ]);

                    $request->session()->flash('success', 'Successfully, '.$description);
                }else{
                    $request->session()->flash('failed', 'Failed, '.$description);
                }
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to convert');
            }
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function history_topup(Request $request)
    {
        $date = $request->date;
        $data = Auth::user()
                ->convert()
                ->where('type','topupcling')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->orderBy('created_at','desc')
                ->paginate(20);
        $total = Auth::user()
                ->convert()
                ->where('type','topupcling')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum('amount');
        $receive = Auth::user()
                ->convert()
                ->where('type','topupcling')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum(DB::raw('receive'));
        return view('backend.convert.history',compact('data','total','receive'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_topup(Request $request)
    {
        $search = $request->search;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        $choose = $request->choose;

        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $status = [0,1,2,3,4];
        $data = Convert::whereIn('status',$status)
                ->where('type','topupcling')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->orderBy('created_at','desc')
                ->paginate(20);

        $total = Convert::whereIn('status',$status)
                ->where('type','topupcling')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('amount');
        $receive = Convert::whereIn('status',$status)
                ->where('type','topupcling')
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($cari) use ($search){
                        $cari->where('users.username', $search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('receive');
        return view('backend.convert.list_topup',compact('data','total','receive'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function checkAccount($username)
    {
        $data = Topup::check($username);
        return response()->json($data);
    }
}
