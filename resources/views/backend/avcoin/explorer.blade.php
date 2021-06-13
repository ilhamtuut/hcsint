@extends('layouts.backend',['page'=>'av_explorer','active'=>'explore'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_globe-2"></i> <span>HCS Techno</span></h4>
@endsection

@section('content')
<div class="col-lg-12 mb-30">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <h5>Latest 10 Blocks</h5>

        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                          <th>Blocks</th>
                          <th class="text-center">Txn</th>
                          <th class="text-right">Volume AV</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($blocks as $value)
                            <tr>
                              <td><a class="text-warning" href="{{route('avcoin.block',$value->block)}}">{{$value->block}}</a></td>
                              <td class="text-center">{{$value->txn}}</td>
                              <td class="text-right">{{number_format($value->av)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="3" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 mb-30">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <h5>Latest Transactions</h5>
        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
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
                              <td class="text-right">{{number_format($value->amount)}} AV</td>
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
