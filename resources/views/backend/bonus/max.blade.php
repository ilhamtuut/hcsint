@extends('layouts.backend',['page'=>'bonus','active'=>'max_profit'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-layers"></i> <span>Maximum Profit</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('bonus.max') }}" method="get" id="form-search">
            <div class="row">
            	<div class="col-md-8"></div>
	            <div class="col-md-4">
	                <div class="form-group">
	                    <div class="input-group">
	                        <input name="search" class="form-control" type="text" placeholder="Search">
	                        <span class="input-group-append">
	                            <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
	                        </span>
	                    </div>
	                </div>
	            </div>
            </div>
          </form>
        </div>

        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                          	<th width="3%">#</th>
                            <th>Username</th>
                            <th>Package</th>
                            <th class="text-right">Maximum Profit</th>
                            <th class="text-center">Percent(%)</th>
                            <th class="text-right">Total Pasif</th>
                            <th class="text-center">Percent(%)</th>
                            <th class="text-right">Total Active</th>
                            <th class="text-center">Percent(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $h)
                            @php
                            $max_prev = 0;
                            $max_profit = $h->max_profit;
                            $aktif = $h->user->bonus_active()->sum('bonus');
                            $total_aktif = $aktif;
                            $pasif = $h->bonus()->sum('bonus');
                            $total = $aktif + $pasif;
                            $max_bonus_paket = DB::table('programs')
                                ->join('packages', 'programs.package_id', '=', 'packages.id')
                                ->where('programs.id', '<=', $h->id)
                                ->where('programs.user_id', $h->user_id)
                                ->sum(DB::raw('packages.amount * packages.max_profit'));
                            if($total >= $max_bonus_paket){
                                $aktif = $max_profit;
                            }else{
                                $max_prev = DB::table('programs')
                                ->join('packages', 'programs.package_id', '=', 'packages.id')
                                ->where('programs.id', '<', $h->id)
                                ->where('programs.user_id', $h->user_id)
                                ->sum(DB::raw('packages.amount * packages.max_profit'));
                                $aktif = $aktif - $max_prev;
                                if($aktif < 0){
                                $aktif = 0;
                                }
                            }
                            $total_bonus = $aktif + $pasif;
                            $percentage_aktif = ($aktif/$max_profit) * 100;
                            $percentage_pasif = ($pasif/$max_profit) * 100;
                            $percentage = (($max_profit - $total_bonus)/$max_profit) * 100;
                            $percentage = $percentage_aktif + $percentage_pasif;
                            @endphp
                            <tr>
                                <td>{{++$i}}</td>
                                <td>{{ucfirst($h->user->username)}}</td>
                                <td>{{$h->package->description}} {{number_format($h->package->amount)}}</td>
                                <td class="text-right">{{number_format($max_profit,2)}}</td>
                                <td class="text-center">{{number_format($percentage,2)}}</td>
                                <td class="text-right">{{number_format($pasif,2)}}</td>
                                <td class="text-center">{{number_format($percentage_pasif,2)}}</td>
                                <td class="text-right">{{number_format($aktif,2)}}</td>
                                <td class="text-center">{{number_format($percentage_aktif,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="9" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {!! $data->render() !!}
        </div>
    </div>
</div>
@endsection
