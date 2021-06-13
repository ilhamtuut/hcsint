@extends('layouts.backend',['page'=>'bonus','active'=>$type])

@section('header')
  <h4 class="font-color-purple"><i class="icon-layers"></i> <span>Bonus {{ucfirst($type)}}</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('bonus.active',$type) }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-8"></div>
              <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input name="date" class="form-control singledate" type="text" placeholder="Date">
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
                            <th>Date</th>
                            @if($type == 'sponsor')
                          	    <th>From</th>
                            @endif
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Percent</th>
                            <th class="text-right">Bonus</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                                <td>{{++$i}}</td>
                                <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
	                          	@if($type == 'sponsor')
	                              <td>{{ucfirst($value->from->username)}}</td>
	                          	@endif
                                <td>{{$value->description}}</td>
                                <td class="text-right">{{number_format($value->amount,2)}}</td>
                                <td class="text-right">{{$value->percent*100}}</td>
                                <td class="text-right">{{number_format($value->bonus,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="{{($type == 'sponsor')? '7' : '6'}}" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="{{($type == 'sponsor')? '6' : '5'}}">Total</td>
                        <td class="text-right">{{number_format($total,2)}}</td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {{$data->render()}}
        </div>
    </div>
</div>
@endsection
