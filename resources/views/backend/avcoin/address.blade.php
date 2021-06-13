@extends('layouts.backend',['page'=>'av_explorer','active'=>'address'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_globe-2"></i> <span>HCS Techno</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <!-- Ibox Content -->
        <div class="ibox-content">
        	<h5 class="text-warning">Address Details</h5>
            <!-- Table Responsive -->
            <img class="mb-3" style="width: 250px;" src="{{$qrCode}}"><br>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                    	<tr>
                    		<td>Balance:</td>
                    		<td>{{$balance}}</td>
                    	</tr>
                    	<tr>
                    		<td>Address:</td>
                    		<td>{{$address}}</td>
                    	</tr>
                    </tbody>
                </table>
            </div>

        	<h5 class="text-warning">Transactions</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                          <th>Txn Hash</th>
                          <th class="text-center">Block</th>
                          <th>Date</th>
                          <th>From</th>
                          <th>To</th>
                          <th class="text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td><a class="text-warning" href="{{route('avcoin.hash',$value->hash)}}">{{substr($value->hash,0,17)}}...</a></td>
                              <td class="text-center">{{$value->block}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td><a class="text-warning" href="{{route('avcoin.address',$value->from_address)}}">{{substr($value->from_address,0,17)}}...</a></td>
                              <td><a class="text-warning" href="{{route('avcoin.address',$value->to_address)}}">{{substr($value->to_address,0,17)}}...</a></td>
                              <td class="text-right">{{$value->amount}} AV</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{$data->render()}}
        </div>
    </div>
</div>
@endsection
