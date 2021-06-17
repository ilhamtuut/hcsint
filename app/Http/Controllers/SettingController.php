<?php

namespace App\Http\Controllers;

use App\Models\Ico;
use App\Models\User;
use App\Models\Bank;
use App\Models\Package;
use App\Models\Setting;
use App\Models\Composition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $data = Setting::where('status',0)->orderBy('name')->get();
        return view('backend.setting.index',compact('data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'amount'=>'required|numeric',
            'pin_authenticator'=>'required'
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            $data = Setting::find($request->id);
            $name = $data->name;
            if($data->type == "%"){
                $amount = $request->amount/100;
                $ket = "Change ".$data->name." value ".($data->value*100)." to ".$request->amount;
            }else{
                $amount = $request->amount;
                $ket = "Change ".$data->name." value ".$data->value." to ".$amount;
            }
            $data->value = $amount;
            $data->save();
            $request->session()->flash('success', 'Successfully, '.$ket);
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

    public function ico()
    {
        $data = Ico::get();
        return view('backend.setting.ico',compact('data'));
    }

    public function updateIco(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'amount'=>'required|numeric',
            'price'=>'required|numeric',
            'min_buy'=>'required|numeric',
            'pin_authenticator'=>'required'
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            $data = Ico::find($request->id)->update($request->except(['id','pin_authenticator']));
            $request->session()->flash('success', 'Successfully, Update data Ico');
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

    public function package()
    {
        $data = Package::get();
        return view('backend.setting.package',compact('data'));
    }

    public function updatePackage(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'roi'=>'required|numeric',
            'sponsor'=>'required|numeric',
            'pairing'=>'required|numeric',
            'max_profit'=>'required|numeric',
            'pin_authenticator'=>'required'
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
        	$request->merge([
        		'roi' => $request->roi/100,
        		'sponsor' => $request->sponsor/100,
        		'pairing' => $request->pairing/100,
        		'max_profit' => $request->max_profit/100
        	]);
            $data = Package::find($request->id)->update($request->except(['id','pin_authenticator']));
            $request->session()->flash('success', 'Successfully, update pacakge');
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

    public function listBank(Request $request)
    {
        $search = $request->search;
        // $data = Bank::when($search, function ($query) use ($search){
        //     $query->wherehas('user', function ($q) use ($search){
        //         $q->where('users.username',$search);
        //     })
        //     ->orWhere('account_name',$search)
        //     ->orWhere('account_number',$search);
        // })->orderBy('account_name')->paginate(20);
        $data = User::when($search, function ($query) use ($search){
            $query->wherehas('bank', function ($q) use ($search){
                $q->where('banks.account_name',$search)
                ->orWhere('banks.account_number',$search);
            })
            ->orWhere('username',$search);
        })->orderBy('username')->paginate(20);
        return view('backend.setting.bank',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function updateBank(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'type' => 'required',
            'pin_authenticator'=>'required'
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            if($request->type == 'update'){
                Bank::find($request->id)->update($request->all());
                $request->session()->flash('success', 'Successfully, update data bank');
            }else{
                Bank::create([
                    'user_id' => $request->id,
                    'bank_name' => $request->bank_name,
                    'account_name' => $request->account_name,
                    'account_number' => $request->account_number,
                ]);
                $request->session()->flash('success', 'Successfully, add data bank');
            }
        }else{
            $request->session()->flash('failed', 'Failed, PIN Authenticator is wrong');
        }
        return redirect()->back();
    }

    public function composition()
    {
        $data = Composition::get();
        return view('backend.setting.komposisi',compact('data'));
    }

    public function updateComposition(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'composition1'=>'required|numeric',
            'composition2'=>'required|numeric',
            'pin_authenticator'=>'required'
        ]);

        $hasPassword = Hash::check($request->pin_authenticator, Auth::user()->trx_password);
        if($hasPassword){
            if($request->composition1 + $request->composition2 == 100){
                $data = Composition::find($request->id)->update([
                    'one' => $request->composition1/100,
                    'two' => $request->composition2/100
                ]);
                $request->session()->flash('success', 'Successfully, Update data composition');
            }else{
                $request->session()->flash('failed', 'Failed, number of compositions must be 100');
            }
        }else{
            $request->session()->flash('failed', 'Failed, Password is wrong');
        }
        return redirect()->back();
    }

}
