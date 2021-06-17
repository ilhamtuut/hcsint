<?php

namespace App\Http\Controllers;

use DB;
use Response;
use App\Helpers\AvCoin;
use App\Models\Tree;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Program;
use App\Models\Package;
use App\Models\Balance;
use App\Models\Downline;
use App\Models\TreeUpline;
use App\Models\Composition;
use App\Models\BonusActive;
use App\Models\TreeDownline;
use App\Models\HistoryTransaction;
use App\Notifications\InfoRegister;
use App\Rules\IsValidEmail;
use App\Rules\IsValidPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidatonFactory;

class TreeController extends Controller
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
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
	    $chek = Tree::where('user_id',Auth::user()->id)->first();
    	$board = 'false';
    	if($chek){
	    	$board = 'true';
    	}

        $downline = Auth::user()
            ->childs()
            ->has('program')
            ->doesntHave('tree')
            ->get();

        if(Auth::user()->hasRole('admin')){
            $downline = User::has('program')
            ->doesntHave('tree')
            ->get();
        }

        $today = date('Y-m-d');
        $right = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'R'])->sum('amount');
        $left = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'L'])->sum('amount');

        $right_today = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'R'])
            ->whereDate('created_at',$today)->sum('amount');
        $left_today = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'L'])
            ->whereDate('created_at',$today)->sum('amount');

        $cf_rights = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'R'])
            ->whereDate('created_at','<',$today)->sum('amount');
        $cf_lefts = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>'L'])
            ->whereDate('created_at','<',$today)->sum('amount');

        $hasil = $cf_lefts - $cf_rights;
        $cf_right = 0;
        $cf_left = $hasil;
        if ($hasil<0){
            $cf_left = 0;
            $cf_right = $cf_rights - $cf_lefts;
        }

    	return view('backend.team.genealogy_tree', compact('board','downline','right','right_today','cf_right','left','left_today','cf_left'));
    }

    public function sponsor_tree($position)
    {
        $data = [];
        $array = array(
            "id"=>Auth::user()->id,
            "username"=>Auth::user()->username
        );
        array_push($data, $array);
        $sponsor = TreeUpline::where(['upline_id'=>Auth::user()->id,'position'=>$position])->get();
        foreach ($sponsor as $key => $value) {
            $array = array(
                "id"=>$value->user_id,
                "username"=>$value->user->username
            );
            array_push($data, $array);
        }
        return Response::json($data);
    }

    public function downline_tree($user_id)
    {

        $downline = User::select('id','username')
            ->where('parent_id',$user_id)
            ->has('program')
            ->doesntHave('tree')
            ->get()
            ->toArray();

        if(Auth::user()->hasRole('admin')){
            $downline = User::select('id','username')
                ->has('program')
                ->doesntHave('tree')
                ->get()
                ->toArray();
        }
        return Response::json($downline);
    }

    // fix
    public function getDataTree($id)
    {
    	$nodeDataArray = array();
    	$tree = Tree::where('user_id',$id)->first();
        if($tree){
        	$left = TreeUpline::where(['upline_id'=>$id, 'position'=>'L'])->sum('amount');
		    $left = number_format($left);
		    $right = TreeUpline::where(['upline_id'=>$id, 'position'=>'R'])->sum('amount');
		    $right = number_format($right);
	        $user = array(
	        	'key'=>$tree->user_id,
	        	'parent'=>$tree->upline_id,
	        	'username'=>ucfirst($tree->user->username),
	        	'foto'=>$tree->user->foto,
	        	'amount'=>$tree->amount,
	        	'position'=>$tree->position,
	        	'left'=>$left,
	        	'right'=>$right
	        );
	        array_push($nodeDataArray, $user);
        	$downline = Tree::where(['upline_id'=>$id])->orderBy('position','asc')->get();
        	if(count($downline) > 0){
	        	foreach ($downline as $key => $value) {
	        		if(count($downline) == 1){
	        			$position = $value->position;
	        			$posisi = "R";
	        			if($position == "R"){
	        				$posisi = "L";
	        				$users = User::where('id',$id)->first();
                            $position_in_parent = $posisi;
                            if($id != Auth::user()->id){
                                $position_in_parent = TreeUpline::select('position')->where(['upline_id'=>Auth::user()->id, 'user_id'=>$id])->first()->position;
                            }
	        				$user = array(
					        	'key'=>0,
					        	'parent'=>intval($id),
					        	'username_parent'=>ucfirst($users->username),
					        	'username'=>"Empty",
					        	'foto'=>null,
					        	'amount'=>0,
					        	'position'=>$posisi,
                                'position_in_parent'=>$position_in_parent,
					        	'left'=>0,
								'right'=>0
					        );
		        			array_push($nodeDataArray, $user);
	        			}
	        		}

	        		$left = TreeUpline::where(['upline_id'=>$value->user_id, 'position'=>'L'])->sum('amount');
				    $left = number_format($left);
				    $right = TreeUpline::where(['upline_id'=>$value->user_id, 'position'=>'R'])->sum('amount');
				    $right = number_format($right);
                    $position_in_parent = TreeUpline::select('position')->where(['upline_id'=>Auth::user()->id, 'user_id'=>$value->user_id])->first()->position;
	        		$user = array(
			        	'key'=>$value->user_id,
			        	'parent'=>$value->upline_id,
			        	'username'=>ucfirst($value->user->username),
			        	'foto'=>$value->user->foto,
			        	'amount'=>$value->amount,
                        'position'=>$value->position,
			        	'position_in_parent'=>$position_in_parent,
			        	'left'=>$left,
			        	'right'=>$right
			        );
			        array_push($nodeDataArray, $user);

			        if(count($downline) == 1){
	        			$position = $value->position;
	        			$posisi = "L";
	        			if($position == "L"){
	        				$posisi = "R";
	        				$users = User::where('id',$id)->first();
                            $position_in_parent = $posisi;
                            if($id != Auth::user()->id){
                                $position_in_parent = TreeUpline::select('position')->where(['upline_id'=>Auth::user()->id, 'user_id'=>$id])->first()->position;
                            }
	        				$user = array(
					        	'key'=>0,
					        	'parent'=>intval($id),
					        	'username_parent'=>ucfirst($users->username),
					        	'username'=>"Empty",
					        	'foto'=>null,
					        	'amount'=>0,
					        	'position'=>$posisi,
                                'position_in_parent'=>$position_in_parent,
					        	'left'=>0,
								'right'=>0
					        );
		        			array_push($nodeDataArray, $user);
	        			}
	        		}

			        $id = $value->user_id;
			    	$userR = $this->getDownline($id,"L");
			    	if(count($userR)>0){
			    		$idR = $userR["key"];
				    	$nodeDataArray[] = $userR;
				    	$position = $userR["position"];
				    	$position = "L";

				    	if($idR>0){
					    	for ($i=0; $i < 2; $i++) {
					    		$userR = $this->getDownline($idR,$position);
					    		$nodeDataArray[] = $userR;
					    		$position = "R";
					    	}
					    }
				    }

				    $userL = $this->getDownline($id,"R");
			    	if(count($userL)>0){
			    		$idL = $userL["key"];
				    	$nodeDataArray[] = $userL;
				    	$position = $userL["position"];
				    	$position = "L";

				    	if($idL>0){
					    	for ($i=0; $i < 2; $i++) {
					    		$userL = $this->getDownline($idL,$position);
					    		$nodeDataArray[] = $userL;
					    		$position = "R";
					    	}
					    }
				    }
	        	}
	        }else{
                $userL = $this->getDownline($id,"L");
                if(count($userL)>0){
                    $nodeDataArray[] = $userL;
                }

	        	$userR = $this->getDownline($id,"R");
		    	if(count($userR)>0){
			    	$nodeDataArray[] = $userR;
			    }
	        }
        }

        $data = array(
    		"class"=> "go.TreeModel",
  			"nodeDataArray"=>$nodeDataArray
    	);
    	return $data;
    }

    public function getDownline($upline_id,$position)
    {
        $position_in_parent = $position;
        if($upline_id != Auth::user()->id){
            $position_in_parent = TreeUpline::select('position')->where(['upline_id'=>Auth::user()->id, 'user_id'=>$upline_id])->first()->position;
        }
    	$users = User::where('id',$upline_id)->first();
    	$user = array(
        	'key'=>0,
        	'parent'=>intval($upline_id),
        	'username_parent'=>ucfirst($users->username),
        	'username'=>"Empty",
        	'foto'=>null,
        	'amount'=>0,
        	'position'=>$position,
            'position_in_parent'=>$position_in_parent,
        	'left'=>0,
			'right'=>0
        );

        $downline = Tree::where(['upline_id'=>$upline_id,'position'=>$position])->first();
        if($downline){
        	$downline_id = $downline->user_id;
        	$left = TreeUpline::where(['upline_id'=>$downline_id, 'position'=>'L'])->sum('amount');
		    $left = number_format($left);
		    $right = TreeUpline::where(['upline_id'=>$downline_id, 'position'=>'R'])->sum('amount');
		    $right = number_format($right);
            $position_in_parent = TreeUpline::select('position')->where(['upline_id'=>$upline_id, 'user_id'=>$downline_id])->first()->position;
        	$user = array(
	        	'key'=>$downline->user_id,
	        	'parent'=>$downline->upline_id,
	        	'username'=>ucfirst($downline->user->username),
	        	'foto'=>$downline->user->foto,
	        	'amount'=>$downline->amount,
	        	'position'=>$downline->position,
                'position_in_parent'=>$position_in_parent,
	        	'left'=>$left,
    			'right'=>$right
	        );
        }

        return $user;
    }

    public function getDataTree1($id)
    {
    	$tree = Tree::where('user_id',$id)->first();
    	$nodeDataArray = array();
    	if($tree){
    		$id = $tree->id;
    		$nodeDataArray = DB::table("trees")
    				->join('users', 'users.id', '=', 'trees.user_id')
                    ->select('user_id as key', 'upline_id as parent', 'amount', 'position','users.username as username')
                    ->where('trees.id','>=',$id)
                    ->offset(2)
                    ->limit(7)
                    ->get();
        }
        $data = array(
    		"class"=> "go.TreeModel",
  			"nodeDataArray"=>$nodeDataArray
    	);
    	return $data;
    }

    public function registerTree(Request $request)
    {
    	$this->validate($request, [
            'upline_id'=>'required',
            'position'=>'required',
            'username'=>'required'
        ]);

        $upline_id = $request->upline_id;
        $position = $request->position;
        $user_id = $request->username;
        $datenow = date('Y-m-d');
        $this->insertTree($upline_id,$user_id,$position,$request);

        $request->session()->flash('success', 'Successfully, Register Tree');
        return redirect()->back();
    }

    public function insertTree($upline_id,$user_id,$position,$request)
    {
        $tree = Tree::where('user_id',$upline_id)
        		->whereIn('status',[0,1])
        		->orderBy('id','asc')
        		->first();
        if($tree){
            $cek_user = Tree::where('user_id',$user_id)->first();
            if(is_null($cek_user)){
                $check_upline = Tree::where('upline_id',$upline_id)->first();
                if(is_null($check_upline)){
                    $status = 1;
                }else{
                    $status = 2;
                }
                $amount = Program::where(['user_id'=>$user_id])->orderBy('id','desc')->max('amount');
                $program = Program::where(['user_id'=>$user_id,'registered_by'=>0])->orderBy('id','desc')->first();
                if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin') || $program){
                    $amount = 0;
                }

                $cek_position = Tree::where(['upline_id'=>$upline_id,'position'=>$position])->first();

                if(is_null($cek_position)){
                    Tree::create([
                        'user_id' => $user_id,
                        'upline_id' => $upline_id,
                        'amount' => $amount,
                        'position' => $position,
                        'status' => 0
                    ]);

                    $tree->status = $status;
                    $tree->save();

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

                    $request->session()->flash('success', 'Successfully, Register Tree');
                }else{
                    $request->session()->flash('failed', 'Failed, Register Tree');
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

    public function viewRegister(Request $request)
    {
        return view('backend.team.register_tree');
    }

    public function saveUserTree(Request $request)
    {
        $this->validate($request, [
            'parent'=>'required',
            'position'=>'required',
            'package'=>'required',
            'wallet'=>'required',
            'sponsor'=>'required',
            'country'=>'required',
            'username' => 'required|unique:users,username|alpha_num|max:17',
            // 'email' => 'required|string|email|max:255',
            'email' => ['required', 'email',new IsValidEmail],
            'pin_authenticator' => ['required', new IsValidPassword],
            'password' => ['required','confirmed', new IsValidPassword],
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
            $uplineTree = TreeUpline::where('user_id',$request->parent)
                ->groupBy('upline_id')->pluck('upline_id')->toArray();
            array_push($uplineTree, intval($request->parent));
            $upline = User::with('program')->has('program')->where('username',$request->sponsor)->first();
            if($upline && in_array($upline->id,$uplineTree)){
                $input =  $request->all();
                $newUser = User::create([
                    'parent_id' => $upline->id,
                    'username' => $input['username'],
                    'email' => $input['email'],
                    'country' => $input['country'],
                    'password' => Hash::make($input['password']),
                    'trx_password' => Hash::make($input['pin_authenticator']),
                    'status' => 1,
                ]);
                $newUser->attachRole('member');

                Balance::create([
                    'user_id' => $newUser->id,
                    'balance' => 0,
                    'status' => 1,
                    'description' => 'HCS Wallet'
                ]);

                Balance::create([
                    'user_id' => $newUser->id,
                    'balance' => 0,
                    'status' => 1,
                    'description' => 'Cash Wallet'
                ]);

                Balance::create([
                    'user_id' => $newUser->id,
                    'balance' => 0,
                    'status' => 1,
                    'description' => 'Register Wallet'
                ]);

                $user_id = $newUser->id;
                $this->saveDownline($user_id, $upline->id);
                // $newUser->notify(new InfoRegister($input));

                $downline = User::find($user_id);
                $desc = ' To '.ucfirst($downline->username);

                $program = Program::create([
                    'user_id' => $user_id,
                    'package_id' => $package->id,
                    'amount' => $amount,
                    'hcs' => $amount_one,
                    'register' => $amount_two,
                    'cash' => $amount_three,
                    'max_profit' => $max_profit,
                    'status' => 0,
                    'registered_by' => Auth::id(),
                    'description' => 'Purchase '.$package->name.' by '.ucfirst($user->username)
                ]);

                if($amount_one > 0){
                    $walletOne->balance = $walletOne->balance - $amount_one;
                    $walletOne->save();

                    HistoryTransaction::create([
                        'balance_id'=>$walletOne->id,
                        'from_id'=>$user_id,
                        'to_id'=>1,
                        'amount'=> $amount_one,
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username),
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
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username).' from '.ucfirst($user->username),
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
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username),
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
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username).' from '.ucfirst($user->username),
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
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username),
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
                        'description'=> 'Purchase '.$package->name.' to '.ucfirst($newUser->username).' from '.ucfirst($user->username),
                        'status'=> 1,
                        'type'=> 'IN'
                    ]);
                }

                $this->bonus_sponsor($user_id,$amount);
                $this->insertTree($request->parent,$user_id,$request->position,$request);
                return redirect()->route('team.network');
            }else{
                $request->session()->flash('failed', 'Failed, sponsor must match the tree path');
            }
        }else{
            $request->session()->flash('failed', 'Failed, You do not have enough funds to Purchase Package');
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

    public function saveDownline($user_id, $upline_id)
    {
        $upline = $upline_id;
        Downline::create([
            'user_id' => $upline,
            'downline_id' => $user_id,
            'status' => 1
        ]);
        for($i = 1; $i <= 5000; $i++){
            $upline =  $this->downlines($upline,$user_id);
            if(is_null($upline)){
                break;
            }else{
                $upline = $upline;
            }
        }
    }

    public function downlines($upline_id,$user_id)
    {
        $check_downline = Downline::where('downline_id',$upline_id)->orderBy('id','asc')->first();
        if($check_downline){
            $upline = $check_downline->user_id;
            Downline::create([
                'user_id' => $upline,
                'downline_id' => $user_id,
                'status' => 1
            ]);
        }else{
            $upline = null;
        }
        return $upline;
    }
}
