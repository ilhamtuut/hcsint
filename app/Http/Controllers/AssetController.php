<?php

namespace App\Http\Controllers;

use DB;
use App\Models\BuyCoin;
use App\Models\Program;
use App\Models\EstimatedAsset;
use Illuminate\Http\Request;

class AssetController extends Controller
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
    public function index()
    {
        return view('backend.assets.index');
    }

    public function getDataChart(Request $request)
	{
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        $isMob = is_numeric(strpos($ua, "mobile"));
        $pages = 30;
        if($isMob){
            $pages = 10;
        }
        $date = $request->date;
		$data = EstimatedAsset::select('sales','withdraw','metatrader',DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s') as date"))
				->when($date, function ($query) use ($date){
					$query->whereDate('created_at',date('Y-m-d', strtotime(str_replace('/', '-', $date))));
				})
				->limit($pages)->orderBy('id','desc')->get();
		return response()->json($data);
	}

    public function getChartReceiveAv(Request $request)
    {
        $data = Program::select(
                DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as date"),
                DB::raw('sum(avc) as total_day'),
                DB::raw("(select sum(avc) from `programs` where DATE_FORMAT(created_at,'%Y-%m-%d') <= date) as total")
            )
            ->groupBy('date')
            ->orderBy('date','desc')
            ->take(7)
            ->get()->toArray();
		return response()->json($data);
    }

    public function getChartBuyAv(Request $request)
    {
        $data = BuyCoin::select(
                DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as date"),
                DB::raw('sum(amount) as total_day'),
                DB::raw("(select sum(amount) from `buy_coins` where DATE_FORMAT(created_at,'%Y-%m-%d') <= date) as total")
            )
            ->groupBy('date')
            ->orderBy('date','desc')
            ->take(7)
            ->get()->toArray();
		return response()->json($data);
    }
}
