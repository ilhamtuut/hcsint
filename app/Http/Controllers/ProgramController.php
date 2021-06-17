<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use App\Models\User;
use App\Models\Tree;
use App\Models\Package;
use App\Models\Balance;
use App\Models\Program;
use App\Models\TreeRest;
use App\Models\TreeUpline;
use App\Models\BonusPasif;
use App\Models\Composition;
use App\Models\BonusActive;
use App\Models\Wallet;
use App\Models\MoveProgram;
use App\Models\TreeDownline;
use App\Models\HistoryTransaction;
use App\Helpers\AvCoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProgramController extends Controller
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
        $plan = true;
        $networker = Auth::user()->program()->whereHas('package', function ($query){
                $query->where('packages.description','Networker');
            })->first();
        if($networker){
            $plan = false;
        }
        return view('backend.program.index',compact('plan'));
    }

    public function by_admin(Request $request)
    {
        $packages = Package::orderBy('amount','asc')->get();
        return view('backend.program.byAdmin',compact('packages'));
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'package'=>'required|numeric',
            'wallet'=>'required',
            // 'agree'=>'required',
            'pin_authenticator'=>'required'
        ]);

        $wallet = $request->wallet;
        $password = $request->pin_authenticator;
        $package_id = $request->package;
        $package = Package::find($package_id);
        $amount = $package->amount;
        $type = $package->description;
        $max = $package->max_profit;
        $max_profit = $amount * $max;
        $user = Auth::user();
        $user_id = $user->id;
        $hasPassword = Hash::check($password,$user->trx_password);
        if($hasPassword){
            $benar = false;
            $walletOne = $user->balance()->where('description','HCS Wallet')->first();
            $walletTwo = $user->balance()->where('description','Register Wallet')->first();
            $walletThree = $user->balance()->where('description','Cash Wallet')->first();
            if($wallet == 1){
                $composition = Composition::where('name','Register 1')->first();
                $amount_one = $amount * $composition->one;
                $amount_two = $amount * $composition->two;
                $amount_three = 0;
                if($amount_one <= $walletOne->balance && $amount_two <= $walletTwo->balance){
                    $benar = true;
                }
            }elseif($wallet == 2){
                $composition = Composition::where('name','Register 2')->first();
                $amount_one = $amount * $composition->one;
                $amount_two = $amount * $composition->two;
                $amount_three = 0;
                if($amount_one <= $walletOne->balance && $amount_two <= $walletTwo->balance){
                    $benar = true;
                }
            }

            if($benar){
                $new = true;
                $run_package = true;
                $cek_program = Program::where('user_id',$user_id)->orderBy('id','desc')->first();
                if($cek_program){
                    $new = false;
                    $type_plan = $cek_program->package->description;
                    if($type_plan == 'Networker' && $type == 'Regular'){
                        $request->session()->flash('failed', 'Failed, Package Networker cannot downgrade to Package Regular');
                        return redirect()->back();
                    }
                    $current_amount = $cek_program->amount;
                    if($amount < $current_amount){
                        $run_package = false;
                        $request->session()->flash('failed', 'Failed, Package must be more than the current package');
                        return redirect()->back();
                    }
                }

                if($run_package){
                    $program = Program::create([
                        'user_id' => $user_id,
                        'package_id' => $package->id,
                        'amount' => $amount,
                        'hcs' => $amount_one,
                        'register' => $amount_two,
                        'cash' => $amount_three,
                        'max_profit' => $max_profit,
                        'status' => 0,
                        'registered_by' => $user_id,
                        'description' => 'Purchase '.$package->name
                    ]);

                    if($amount_one > 0){
                        $walletOne->balance = $walletOne->balance - $amount_one;
                        $walletOne->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletOne->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_one,
                            'description'=> 'Purchase '.$package->name,
                            'status'=> 1,
                            'type'=> 'OUT'
                        ]);

                        $walletOne_admin = Balance::where(['user_id'=>1,'description'=>'HCS Wallet'])->first();
                        $walletOne_admin->balance = $walletOne_admin->balance + $amount_one;
                        $walletOne_admin->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletOne_admin->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_one,
                            'description'=> 'Purchase '.$package->name.' from '.ucfirst($user->username),
                            'status'=> 1,
                            'type'=> 'IN'
                        ]);
                    }

                    if($amount_two > 0){
                        $walletTwo->balance = $walletTwo->balance - $amount_two;
                        $walletTwo->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletTwo->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_two,
                            'description'=> 'Purchase '.$package->name,
                            'status'=> 1,
                            'type'=> 'OUT'
                        ]);

                        $walletTwo_admin = Balance::where(['user_id'=>1,'description'=>'Register Wallet'])->first();
                        $walletTwo_admin->balance = $walletTwo_admin->balance + $amount_two;
                        $walletTwo_admin->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletTwo_admin->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_two,
                            'description'=> 'Purchase '.$package->name.' from '.ucfirst($user->username),
                            'status'=> 1,
                            'type'=> 'IN'
                        ]);
                    }

                    if($amount_three > 0){
                        $walletThree->balance = $walletThree->balance - $amount_three;
                        $walletThree->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletThree->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_three,
                            'description'=> 'Purchase '.$package->name,
                            'status'=> 1,
                            'type'=> 'OUT'
                        ]);

                        $walletThree_admin = Balance::where(['user_id'=>1,'description'=>'Cash Wallet'])->first();
                        $walletThree_admin->balance = $walletThree_admin->balance + $amount_three;
                        $walletThree_admin->save();

                        HistoryTransaction::create([
                            'balance_id'=>$walletThree_admin->id,
                            'from_id'=>$user_id,
                            'to_id'=>1,
                            'amount'=> $amount_three,
                            'description'=> 'Purchase '.$package->name.' from '.ucfirst($user->username),
                            'status'=> 1,
                            'type'=> 'IN'
                        ]);
                    }

                    $this->bonus_sponsor($user_id,$amount);
                    if(!$new){
                        $this->updateTree($user_id, $amount);
                    }
                    $request->session()->flash('success', 'Successfully Purchase Package');
                    return redirect()->back()->with(['program'=>$program]);
                }
            }else{
                $request->session()->flash('failed', 'Failed, You do not have enough funds to Purchase Package');
            }
        }else {
            $request->session()->flash('failed', 'Failed, pin authenticator is wrong.');
        }

        return redirect()->back();
    }

    public function register_byadmin(Request $request)
    {
        $this->validate($request, [
            'package'=>'required',
            'username'=>'required',
            'pin_authenticator'=>'required'
        ]);

        $package_id = $request->package;
        $password = $request->pin_authenticator;
        $username = $request->username;
        $package = Package::find($package_id);
        $amount = $package->amount;
        $max = $package->max_profit;
        $max_profit = $amount * $max;
        $users = Auth::user();
        $hasPassword = Hash::check($password,$users->trx_password);
        if($hasPassword){
            $user = User::where('username',$username)->first();
            if($user){
                $wallet = Auth::user()->balance()->where('description','HCS Wallet')->first();
                if($amount <= $wallet->balance){
                    $upline = User::with('program')->has('program')->where('id',$user->parent_id)->first();
                    if($upline){
                        // $check = Program::where(['user_id'=>$user->id,'registered_by'=>0])->first();
                        // if(is_null($check)){
                            Program::create([
                                'user_id' => $user->id,
                                'package_id' => $package->id,
                                'amount' => $amount,
                                'hcs' => $amount,
                                'max_profit' => $max_profit,
                                'status' => 2,
                                'registered_by' => 0,
                                'description' => 'Purchase '.$package->name
                            ]);

                            $wallet->balance = $wallet->balance - $amount;
                            $wallet->save();

                            HistoryTransaction::create([
                                'balance_id'=> $wallet->id,
                                'from_id'=> Auth::user()->id,
                                'to_id'=> 1,
                                'amount'=> $amount,
                                'description'=> 'Purchase '.$package->name.' to '.ucfirst($username),
                                'status'=> 1,
                                'type'=> 'OUT'
                            ]);

                            $wallet_admin = Balance::where(['user_id'=>1,'description'=>'HCS Wallet'])->first();
                            $wallet_admin->balance = $wallet_admin->balance + $amount;
                            $wallet_admin->save();

                            HistoryTransaction::create([
                                'balance_id' => $wallet_admin->id,
                                'from_id' => Auth::user()->id,
                                'to_id' => 1,
                                'amount' => $amount,
                                'description' => 'Purchase '.$package->name.' from '.ucfirst($username),
                                'status' => 1,
                                'type' => 'IN'
                            ]);

                            $request->session()->flash('success', 'Successfully, Purchase Package for '.$username);
                        // }else{
                        //     $request->session()->flash('failed', 'Failed, Purchase Package only once.');
                        // }
                    }else{
                        $request->session()->flash('failed', 'Failed, Referal has not yet registered package');
                    }
                }else{
                    $request->session()->flash('failed', 'Failed, HCS Wallet not enough to register package.');
                }
            }else{
                $request->session()->flash('failed', 'Failed, Username not found.');
            }
        }else {
            $request->session()->flash('failed', 'Failed, pin authenticator is wrong.');
        }
        return redirect()->back();
    }

    public function bonus_sponsor($user_id,$amount)
    {
        $user = User::find($user_id);
        $upline_id = $user->parent_id;
        $programUpline = Program::where(['user_id'=>$upline_id])
                ->whereIn('status',[0,2])->orderBy('id','desc')->first();
        if($programUpline){
            $percent = $programUpline->package->sponsor;
            $bonus = $amount * $percent;
            if($bonus > 0){
                $user = User::find($upline_id);
                $maxBonus = $user->is_max($bonus);
                if($maxBonus['max_profit']){
                    $bonus = $maxBonus['bonus'];
                    $lost = $maxBonus['lost'];
                    BonusActive::create([
                        'user_id' => $upline_id,
                        'from_id' => $user_id,
                        'amount' => $amount,
                        'percent' => $percent,
                        'bonus' => $bonus,
                        'lost' => $lost,
                        'status' => 1,
                        'description' => 'Bonus Sponsor'
                    ]);

                    $wallet_a1 = Balance::where(['user_id' => 1, 'description' => 'Register Wallet'])->first();
                    $wallet_a1->balance = $wallet_a1->balance - $bonus;
                    $wallet_a1->save();
                    $history = HistoryTransaction::create([
                        'balance_id'=> $wallet_a1->id,
                        'from_id'=> 1,
                        'to_id'=> $upline_id,
                        'amount'=> $bonus,
                        'description'=> 'Bonus Sponsor to '.ucfirst($user->username),
                        'status'=> 1,
                        'type'=> 'OUT'
                    ]);

                    $wallet_satu = $user->balance()->where('description','Register Wallet')->first();
                    $wallet_satu->balance = $wallet_satu->balance + $bonus;
                    $wallet_satu->save();
                    $history = HistoryTransaction::create([
                        'balance_id'=> $wallet_satu->id,
                        'from_id'=> $user_id,
                        'to_id'=> $upline_id,
                        'amount'=> $bonus,
                        'description'=> 'Bonus Sponsor',
                        'status'=> 1,
                        'type'=> 'IN'
                    ]);
                }
            }
        }
    }

    public function profit_capital(Request $request,$type,$desc,$id)
    {
        $program = Program::find($id);
        if($type == 'profit'){
            if($desc == 'run'){
                $status = 0;
                $activity = 'Running Profit Username '.ucfirst($program->user->username);
            }elseif($desc == 'stop'){
                $status = 2;
                $activity = 'Stop Profit Username '.ucfirst($program->user->username);
            }
            $program->status = $status;
        }
        $program->save();

        $request->session()->flash('success', 'Successfully, '.$activity);
        return Response::json(['success'=>1]);
    }

    public function history(Request $request)
    {
        $date = $request->date;
        $data = Auth::user()->program()
                ->when($date, function ($query) use ($date){
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    $query->whereDate('created_at',$date);
                })
                ->orderBy('id','desc')
                ->paginate(20);
        $total = Auth::user()->program()
                ->when($date, function ($query) use ($date){
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    $query->whereDate('created_at',$date);
                })
                ->sum('amount');
        $total_max = Auth::user()->program()
                ->when($date, function ($query) use ($date){
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    $query->whereDate('created_at',$date);
                })
                ->sum('max_profit');
        return view('backend.program.history',compact('data','total','total_max'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_program(Request $request,$regby)
    {
        $search = $request->search;
        $status = $request->status;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $by = '!=';
        $active = 'list_package_member';
        if($regby == 'admin'){
            $by = '=';
            $active = 'list_package_admin';
        }

        $whereIn = [0,1,2];
        if($status == 1){
            $whereIn = [0];
        }elseif($status == 2){
            $whereIn = [1];
        }elseif($status == 3){
            $whereIn = [2];
        }

        $data = Program::where('user_id','!=',2)
            ->when($search, function ($query) use ($search){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username',$search);
                });
            })
            ->when($status, function ($query) use ($whereIn){
                $query->whereIn('status',$whereIn);
            })
            ->where('registered_by',$by,0)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->orderBy('id','desc')
            ->paginate(20);

        $total = Program::where('user_id','!=',2)
            ->when($search, function ($query) use ($search){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username',$search);
                });
            })
            ->when($status, function ($query) use ($whereIn){
                $query->whereIn('status',$whereIn);
            })
            ->where('registered_by',$by,0)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('amount');
        $total_profit = Program::where('user_id','!=',2)
            ->when($search, function ($query) use ($search){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username',$search);
                });
            })
            ->when($status, function ($query) use ($whereIn){
                $query->whereIn('status',$whereIn);
            })
            ->where('registered_by',$by,0)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('max_profit');
        $hal = 'member';
        return view('backend.program.list',compact('data','from_date','to_date','search','total','hal','regby','active','status','total_profit'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_av(Request $request)
    {
        $search = $request->search;
        $from_date = str_replace('/', '-', $request->from_date);
        $to_date = str_replace('/', '-', $request->to_date);
        if($from_date && $to_date){
            $from = date('Y-m-d',strtotime($from_date));
            $to = date('Y-m-d',strtotime($to_date));
        }else{
            $from = date('Y-m-d',strtotime('01/01/2018'));
            $to = date('Y-m-d');
            $from_date = '01/01/2018';
            $to_date = date('d/m/Y');
        }

        $data = Program::where('user_id','!=',2)
            ->when($search, function ($query) use ($search){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username',$search);
                });
            })
            ->where('registered_by','>',0)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->orderBy('id','desc')
            ->paginate(20);

        $total = Program::where('user_id','!=',2)
            ->when($search, function ($query) use ($search){
                $query->whereHas('user', function ($cari) use ($search){
                    $cari->where('users.username',$search);
                });
            })
            ->where('registered_by','>',0)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('avc');
        return view('backend.program.invest_av',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function updateTree($user_id, $amount)
    {
        $cek_tree = Tree::where('user_id',$user_id)->first();
        if($cek_tree){
            $position = $cek_tree->position;
            $upline_id = $cek_tree->upline_id;
            $cek_tree->amount = $amount;
            $cek_tree->save();

            TreeUpline::create([
                'user_id' => $user_id,
                'upline_id' => $upline_id,
                'amount' => $amount,
                'position' => $position,
                'status' => 1
            ]);

            TreeDownline::create([
                'user_id' => $upline_id,
                'downline_id' => $user_id,
                'amount' => $amount,
                'position' => $position,
                'status' => 1
            ]);

            $upline = $upline_id;
            for($i = 1; $i <= 1000; $i++){
                $upline =  $this->TreeDownline($upline,$user_id,$amount);
                if(is_null($upline)){
                    break;
                }else{
                    $upline = $upline;
                }
            }
        }
    }

    public function TreeDownline($upline_id,$user_id,$amount)
    {
        $check_downline = TreeDownline::where('downline_id',$upline_id)->orderBy('id','asc')->first();
        if($check_downline){
            $upline = $check_downline->user_id;
            $position = $check_downline->position;
            TreeDownline::create([
                'user_id' => $upline,
                'downline_id' => $user_id,
                'amount' => $amount,
                'position' => $position,
                'status' => 1
            ]);

            TreeUpline::create([
                'user_id' => $user_id,
                'upline_id' => $upline,
                'amount' => $amount,
                'position' => $position,
                'status' => 1
            ]);
        }else{
            $upline = null;
        }
        return $upline;
    }

    public function getPlan(Request $request, $type)
    {
        $type_name = 'Register 1';
        if($type == 'Networker'){
            $type_name = 'Register 2';
        }
        $plan = Package::where('description', $type)->orderBy('amount')->get();
        $composition = Composition::where('name',$type_name)->get();
        return response()->json(['success'=>true, 'plan' => $plan, 'composition' => $composition]);
    }

    public function moveProgram(Request $request,$id)
    {
        $program = Program::find($id);
        if($program->package->description == 'Regular'){
            $amount = $program->amount;
            $old_package_id = $program->package_id;
            $new_package = Package::where(['amount'=>$amount,'description'=>'Networker'])->first();
            if($new_package){
                $new_package_id = $new_package->id;
                $program->update([
                    'package_id' => $new_package_id
                ]);

                MoveProgram::create([
                    'program_id' => $id,
                    'old_package_id' => $old_package_id,
                    'new_package_id' => $new_package_id
                ]);
                $request->session()->flash('success', 'Successfully move to Package Networker.');
            }else{
                $request->session()->flash('failed', 'Failed, Package Networker Not Found.');
            }
        }else{
            $request->session()->flash('failed', 'Failed, only Package Regular.');
        }
        return response()->json(['success'=>true]);
    }
}
