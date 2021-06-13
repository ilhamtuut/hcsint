<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Video;
use App\Models\MetaTrader;
use Illuminate\Http\Request;

class MetatraderController extends Controller
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
        // return view('mt');
        if(Auth::user()->hasRole('admin') && !Auth::user()->program()->exists() && !Auth::user()->tree()->exists()){
            return redirect()->route('home');
        }
        $accountID = $request->accountID;
        $data = MetaTrader::get();
        $account = MetaTrader::when($accountID, function ($query) use ($accountID){
                    $query->where('accountID',$accountID);
                })->first();
        $video = Video::orderBy('id','asc')->paginate(20);
        return view('backend.metatrader.index',compact('data','account','video'));
    }

    public function list(Request $request)
    {
        $search = $request->search;
        $data = MetaTrader::when($search, function ($query) use ($search){
                $query->where('accountID', $search);
            })->paginate(20);
        return view('backend.metatrader.list',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required',
            'accountID'=>'required',
            'password'=>'required',
            'server'=>'required',
            'type'=>'required'
        ]);
        MetaTrader::create($request->all());
        $request->session()->flash('success', 'Successfully add data MetaTrader');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'=>'required',
            'accountID'=>'required',
            'password'=>'required',
            'server'=>'required',
            'type'=>'required'
        ]);
        MetaTrader::find($id)->update($request->all());
        $request->session()->flash('success', 'Successfully update data MetaTrader');
        return redirect()->back();
    }

    public function delete(Request $request, $id)
    {
        MetaTrader::find($id)->delete();
        $request->session()->flash('success', 'Successfully delete data MetaTrader');
        return redirect()->back();
    }
}
