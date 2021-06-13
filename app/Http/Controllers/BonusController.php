<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Program;
use Illuminate\Http\Request;

class BonusController extends Controller
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

    public function pasif(Request $request)
    {
        $date = $request->date;
        $data = Auth::user()
                ->bonus_pasif()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->orderBy('id','desc')
                ->paginate(20);
        $total = Auth::user()
                ->bonus_pasif()
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum('bonus');
        return view('backend.bonus.pasif',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function active(Request $request, $type)
    {
        $date = $request->date;
        $data = Auth::user()
                ->bonus_active()
                ->where('description','like','%'.str_replace('_', ' ', $type).'%')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->orderBy('id','desc')
                ->paginate(20);
        $total = Auth::user()
                ->bonus_active()
                ->where('description','like','%'.str_replace('_', ' ', $type).'%')
                ->when($date,function ($cari) use ($date) {
                    $date = date('Y-m-d',strtotime(str_replace('/', '-', $date)));
                    return $cari->whereDate('created_at', $date);
                })
                ->sum('bonus');
        return view('backend.bonus.active',compact('data','total','type'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function list(Request $request,$desc)
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
            $from_date = date('01/01/Y');
            $to_date = date('d/m/Y');
        }

        $active = '';
        $ket = '';
        $bonus ='App\Models\BonusActive';
        if($desc == 'sponsor'){
            $ket = '%sponsor%';
            $active = 'list_sponsor';
        }elseif($desc == 'roi'){
            $ket = '%%';
            $bonus ='App\Models\BonusPasif';
            $active = 'list_roi';
        }elseif($desc == 'pairing'){
            $ket = '%pairing%';
            $active = 'list_pairing';
        }

        if($ket){
            $data = $bonus::where('description','like',$ket)
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($q) use ($search){
                        $q->where('users.username',$search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->orderBy('id','desc')
                ->paginate(20);

            $total = $bonus::where('description','like',$ket)
                ->when($search, function ($query) use ($search){
                    $query->whereHas('user', function ($q) use ($search){
                        $q->where('users.username',$search);
                    });
                })
                ->whereDate('created_at','>=',$from)
                ->whereDate('created_at','<=',$to)
                ->sum('bonus');

            return view('backend.bonus.list',compact('data','total','desc','active'))->with('i', (request()->input('page', 1) - 1) * 20);
        }else{
            return redirect()->route('home');
        }
    }

    public function max(Request $request)
    {
        $search = $request->search;
        $data = Program::when($search, function ($query) use ($search){
                $query->whereHas('user', function ($q) use ($search){
                    $q->where('users.username',$search);
                });
            })
            ->orderBy('id','desc')
            ->paginate(20);
        return view('backend.bonus.max',compact('data'))->with('i', (request()->input('page', 1) - 1) * 20);

    }
}
