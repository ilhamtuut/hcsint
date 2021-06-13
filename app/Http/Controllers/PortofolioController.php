<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
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
        if(!$type){
            $type = 'Regular';
        }
        $data = Portofolio::where('type',$type)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->orderBy('id','desc')->paginate(30);
        $total = Portofolio::where('type',$type)
            ->whereDate('created_at','>=',$from)
            ->whereDate('created_at','<=',$to)
            ->sum('percent');
        return view('backend.portofolio.index',compact('data','total'))->with('i', (request()->input('page', 1) - 1) * 20);
    }
}
