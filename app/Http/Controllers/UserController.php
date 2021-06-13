<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Response;
use App\Models\Role;
use App\Models\Bank;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Balance;
use App\Models\Downline;
use Illuminate\Http\Request;
use App\Rules\IsValidEmail;
use App\Rules\IsValidPassword;
use App\Notifications\ResetPin;
use Illuminate\Support\Facades\URL;
use App\Notifications\ResetQuestion;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        $roles = Role::select('display_name','id')->orderBy('name')->get();
        return view('backend.user.create',compact('roles'));
    }

    public function profile(Request $request)
    {
        return view('backend.user.profile');
    }

    public function edit_profile(Request $request)
    {
        return view('backend.user.editprofile');
    }

    public function inputData(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            // 'email' => 'nullable|email'
        ]);

        $user = Auth::user();
        $user->update($request->all());
        $request->session()->flash('success', 'Successfully updated data profile');
        return redirect()->back();
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required|string|min:8',
            'new_password' => ['required', new IsValidPassword],
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        $users = Auth::user();
        $password = $request->current_password;
        $hasPassword = Hash::check($password,$users->password);
        if ($hasPassword){
            $users->fill([
                'password' => Hash::make($request->new_password)
            ]);
            $users->save();

            $request->session()->flash('success', 'Successfully, updated password');
            return redirect()->back();
        }else {
            $request->session()->flash('failed', 'Failed, password is wrong.');
            return redirect()->back();
        }
    }

    public function updatePasswordtrx(Request $request)
    {
        $this->validate($request, [
            'current_pin_authenticator' => 'required',
            'new_pin_authenticator' => ['required', new isValidPassword],
            'confirm_pin_authenticator' => 'required|string|min:8|same:new_pin_authenticator',
        ]);

        $users = Auth::user();
        $password = $request->current_pin_authenticator;
        $hasPassword = Hash::check($password,$users->trx_password);
        if ($hasPassword){
            $users->fill([
                'trx_password' => Hash::make($request->new_pin_authenticator)
            ]);
            $users->save();
            $request->session()->flash('success', 'Successfully, updated PIN Authenticator');
            return redirect()->back();
        }else {
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong.');
            return redirect()->back();
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'sponsor'=>'required',
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users,username|alpha_num|max:17',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            // 'email' => 'required|string',
            'email' => ['required', 'email',new IsValidEmail],
            'role' => 'required',
            'pin_authenticator' => ['required', new isValidPassword],
            'password' => ['required', new isValidPassword],
        ]);
        // $upline = User::where('username',$request->sponsor)->first();
        $upline = User::with('program')->has('program')->where('username',$request->sponsor)->first();
        if($upline){
            $data = $input = $request->all();
            $user = User::create([
                'parent_id' => $upline->id,
                'name' => $data['name'],
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
                'country' => $data['country'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'trx_password' => Hash::make($data['pin_authenticator']),
                'status' => 1,
            ]);
            $user->roles()->attach($request->role);

            Balance::create([
                'user_id' => $user->id,
                'balance' => 0,
                'status' => 1,
                'description' => 'HCS Wallet'
            ]);

            Balance::create([
                'user_id' => $user->id,
                'balance' => 0,
                'status' => 1,
                'description' => 'Cash Wallet'
            ]);

            Balance::create([
                'user_id' => $user->id,
                'balance' => 0,
                'status' => 1,
                'description' => 'Register Wallet'
            ]);

            $this->saveDownline($user->id, $upline->id);

            $request->session()->flash('success', 'Successfully, create data user');

        }else{
            $request->session()->flash('failed', 'Failed Referal has not yet registered package or not found');
        }
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $role_user = $request->session()->get('roles');
        $roles = Role::select('display_name', 'id')->get();
        $user = User::find($id);
        return view('backend.user.edit', compact('user', 'roles', 'role_user'));
    }

    public function updateData(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'email' => 'required|string',
            'role' => 'required',
        ]);

        $users = User::find($id);
        $input = $request->all();
        $users->update($input);

        $users->roles()->sync($request->role);
        $request->session()->flash('success', 'Successfully updated data username '.$users->username);

        return redirect()->to('user/list/'.$request->session()->get('roles'));
    }

    public function list(Request $request,$role_name)
    {
        $search = $request->search;
        $status = $request->status;
        $data = User::whereHas('roles', function ($query) use($role_name) {
                    $query->where('roles.name', $role_name);
                })
                ->when($search, function ($cari) use ($search) {
                    return $cari->where('username', 'LIKE' ,$search.'%')
                    ->orWhere('name', 'LIKE', $search.'%')
                    ->orWhere('email', 'LIKE', $search.'%');
                })
                ->when($status, function ($cari) use ($status) {
                    return $cari->where('status', $status);
                })->paginate(20);
        $role = $role_name;
        $request->session()->put('roles', $role);
        return view('backend.user.list', compact('data', 'role'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function getUsername(Request $request)
    {
        $results = array('error' => false, 'data' => '');
        $search = $request->search;
        if($search){
            $data = DB::table("users")
                    ->select("id","username")
                    ->whereNotIn('id',[1,2])
                    ->where('username','LIKE',"$search%")
                    ->get();
            if(count($data) > 0){
                foreach ($data as $key => $value) {
                    $results['data'] .= "
                        <li class='list-gpfrm-list' data-fullname='".ucfirst($value->username)."' data-id='".$value->id."'>".ucfirst($value->username)."</li>
                    ";
                }
            }else{
                $results['data'] = "
                    <li class='list-gpfrm-list'>No found data matches Records</li>
                ";
            }
        }else{
            $results['error'] = true;
        }
        echo json_encode($results);
    }

    public function searchUser(Request $request)
    {
        $username = $request->username;
        $user = User::select('id','username')->where('username',$username)->first();
        $results = array('error' => false, 'data' => '');
        if($user){
            $results['data'] = $user;
        }else{
            $results['error'] = true;
        }
        return Response::json($results);
    }

    public function block_unclock(Request $request,$id)
    {
        $user = User::find($id);
        $status = $user->status;
        if($status == 2){
            $block = 1;
            $msg = 'Activated username '.$user->username;
        }else{
            $block = 2;
            $msg = 'Suspended username '.$user->username;
        }
        $user->status = $block;
        $user->save();

        $request->session()->flash('success', 'Successfully, '.$msg);
        return redirect()->back();
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

    public function list_sponsor(Request $request)
    {
        $search = $request->search;
        $data = User::whereNotIn('id',[1])
                ->when($search,function ($cari) use ($search) {
                    return $cari->where('username', $search);
                })->paginate(20);

        return view('backend.user.list_sponsor',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_donwline(Request $request)
    {
        $search = $request->search;
        $data = Auth::user()->childs()
            ->when($search,function ($cari) use ($search) {
                return $cari->where('username', 'LIKE', $search.'%');
            })->paginate(20);
        $id = Auth::user()->id;
        $username = Auth::user()->username;
        return view('backend.user.list_downline',compact('data','date','id','username'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list_donwline_user(Request $request,$id)
    {
        $search = $request->search;
        $user = User::find($id);
        if($user){
            $username = $user->username;
            $data = User::where('parent_id',$id)
                    ->when($search,function ($cari) use ($search) {
                        return $cari->where('username', 'LIKE', $search.'%');
                    })->paginate(20);

            return view('backend.user.list_downline',compact('data','id','username'))->with('i', (request()->input('page', 1) - 1) * 20);
        }else{
            return redirect()->back();
        }
    }

    public function wallets(Request $request)
    {
        $search = $request->search;
        $data = Wallet::when($search,function ($query) use ($search) {
                        $query->whereHas('user', function ($q) use ($search) {
                            $q->where('users.username',$search);
                        });
                    })->paginate(20);
        return view('backend.user.list_wallet',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function resetPin(Request $request)
    {
        $user = Auth::user();
        $pin = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1,8))), 1, 8);
        $user->trx_password = Hash::make($pin);
        $user->save();
        $user->notify(new ResetPin($pin));
        $request->session()->flash('success', 'Success, Reset your Authenticator PIN, check your email to see Authenticator PIN');
        return redirect()->back();
    }

    public function viewBank(Request $request)
    {
        return view('backend.user.bank');
    }

    public function saveBank(Request $request)
    {
        $this->validate($request, [
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required'
        ]);
        // ASRIANTI => 7935484847
        // HASLINDA => 6595051839
        if($request->account_name == 'ASRIANTI' || $request->account_name == 'HASLINDA' || $request->account_number == '7935484847' || $request->account_number == '6595051839'){
            Auth::user()->status = 2;
            Auth::user()->save();
            $this->guard()->logout();
            return redirect('login');
        }

        Bank::create([
            'user_id'=>Auth::id(),
            'bank_name'=>$request->bank_name,
            'account_name'=>$request->account_name,
            'account_number'=>$request->account_number
        ]);
        $request->session()->flash('success', 'Successfully add data bank');
        return redirect('home');
    }

    public function resetQuestion(Request $request,$user_id)
    {
        $user = User::find($user_id);
        $url = URL::temporarySignedRoute(
            'question.viewAnswer',
            now()->addMinutes(30),
            ['user' => $user->id]
        );
        $user->notify(new ResetQuestion($url));
        $request->session()->flash('success', 'Successfully, send question reset link to email member');
        return redirect()->back();
    }
}
