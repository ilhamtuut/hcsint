@extends('layouts.backend',['page'=>'av_explorer','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_globe-2"></i> <span>HCS Techno</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <!-- Ibox Content -->
        <div class="ibox-content">
        	<h5 class="text-warning">Transaction Details</h5>
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                    	<tr>
                    		<td>Transaction Hash:</td>
                    		<td>{{$data->hash}}</td>
                    	</tr>
                    	<tr>
                    		<td>Status:</td>
                    		<td><span class="badge p-1 badge-success">Success</span></td>
                    	</tr>
                    	<tr>
                    		<td>Block:</td>
                    		<td>{{$data->block}}</td>
                    	</tr>
                    	<tr>
                    		<td>Timestamp:</td>
                    		<td>{{$data->created_at}}</td>
                    	</tr>
                    	<tr>
                    		<td>From:</td>
                    		<td><a class="text-warning" href="{{route('avcoin.address',$data->from_address)}}">{{$data->from_address}}</a></td>
                    	</tr>
                    	<tr>
                    		<td>To:</td>
                    		<td><a class="text-warning" href="{{route('avcoin.address',$data->to_address)}}">{{$data->to_address}}</a></td>
                    	</tr>
                    	<tr>
                    		<td>Value:</td>
                    		<td>{{$data->amount}} AV</td>
                    	</tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
