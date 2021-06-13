<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Ico;
use App\Models\Program;
use App\Models\BuyCoin;
use App\Helpers\AvCoin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('block-user');
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
        $user = Auth::user();
        $today = date('Y-m-d');
        $hcs = number_format($user->balance()->where('description','HCS Wallet')->first()->balance,2);
        $cash = number_format($user->balance()->where('description','Cash Wallet')->first()->balance,2);
        $register = number_format($user->balance()->where('description','Register Wallet')->first()->balance,2);

        $bonus_aktif = $user->bonus_active()->sum('bonus');
        $bonus_pasif = $user->bonus_pasif()->sum('bonus');
        $todayaktif = $user->bonus_active()->whereDate('created_at',$today)->sum('bonus');
        $todaypasif = $user->bonus_pasif()->whereDate('created_at',$today)->sum('bonus');
        $totalEarn = number_format(($bonus_aktif + $bonus_pasif),2);
        $todayEarn = number_format(($todayaktif + $todaypasif),2);

        $package = '0.00';
        $max_profit = number_format($user->program()->sum('max_profit'),2);
        $package_user = $user->program()->where('status','!=',1)->orderBy('id','desc')->first();
        if($package_user){
            $package = $package_user->package->description." ".number_format($package_user->package->amount,2);
        }
        return view('home',compact('hcs','cash','register','package','todayEarn','totalEarn','max_profit'));
    }

    public function count_down()
    {
        $date = env('SET_DATE');
        $set_date = date('Y/m/d',strtotime('+100 day', strtotime($date)));
        return view('count_down', compact('set_date'));
    }
}
