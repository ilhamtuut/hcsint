<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Balance;
use App\Models\Downline;
use Illuminate\Http\Request;
use App\Rules\IsValidEmail;
use App\Rules\IsValidPassword;
use App\Notifications\InfoRegister;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'referral' => ['required', 'string', 'max:255'],
            // 'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', new IsValidEmail],
            'country' => ['required', 'string', 'max:255'],
            // 'phone_number' => ['required', 'string', 'max:255'],
            'pin_authenticator' => ['required', new IsValidPassword],
            'password' => ['required','confirmed', new IsValidPassword],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // $parent_id = substr($data['referral'],10);
        $parent_id = User::where('username',$data['referral'])->first()->id;
        $user = User::create([
            'parent_id' => $parent_id,
            // 'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            // 'phone_number' => $data['phone_number'],
            'country' => $data['country'],
            'password' => Hash::make($data['password']),
            'trx_password' => Hash::make($data['pin_authenticator']),
            'email_verified_at' => now()
        ]);
        $user->attachRole('member');

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
        if($parent_id){
            $this->saveDownline($user->id, $parent_id);
        }
        $user->notify(new InfoRegister($data));
        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        // $user_id = substr($request->referral,10);
        // $user = User::find($user_id);
        $user = User::where('username',$request->referral)->first();
        if($user){
                event(new Registered($user = $this->create($request->all())));
                $this->registered($request, $user)
                            ?: redirect($this->redirectPath());
                $request->session()->flash('success', 'Successfully register account, please login to account.');
                return redirect('/login');
        }else{
            $request->session()->flash('failed', 'Referal has not found.');
            return redirect()->back();
        }
    }

    public function referal(Request $request,$referral)
    {
        // $user_id = substr($referral,10);
        // $user = User::find($user_id);
        $user = User::where('username',$referral)->first();
        if($user){
            $request->session()->put('ref:user:username', $referral);
            return redirect()->route('register');
        }else{
            $request->session()->flash('failed', 'Referral has not found');
            return redirect()->route('login');
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
