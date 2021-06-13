@extends('layouts.backend',['page'=>'av_coin','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_lifesaver"></i> <span>AV</span></h4>
@endsection

@section('content')
	<div class="col-lg-12">
    	@include('layouts.partials.alert')
	</div>
	<div class="col-lg-6 mb-30">
	    <!-- Ibox -->
	    <div class="ibox-home bg-boxshadow">
	        <!-- Ibox Content -->
	        <div class="ibox-content">
	        	<h5 class="text-warning">Your Address</h5>
	        	<hr>
	        	<div class="text-center">
		            <img class="mb-3" style="width: 250px;" src="{{$qrCode}}"><br>
	              	<p class="text-muted">{{$address}} <span class="btn m-2 btn-xs btn-outline-warning" onclick="copyToClipboard('{{$address}}')"><i class="fa fa-copy cursor-pointer"></i></span></p>
              	</div>
	        </div>
	    </div>
	</div>
	<div class="col-lg-6 mb-30">
	    <!-- Ibox -->
	    <div class="ibox-home bg-boxshadow">
	        <!-- Ibox Content -->
	        <div class="ibox-content">
	        	<h5 class="text-warning">Send AV</h5>
	        	<hr>
	        	<p>Balance : {{$balance}} AV</p>
	        	@role('member')
		            <form class="form-horizontal form-label-left" action="{{route('avcoin.send')}}" method="POST">
		                @csrf
	                    <div class="form-group">
	                        <label>Address</label>
	                        <input id="address" type="text" name="address" placeholder="Address" class="form-control">
	                    </div>

	                    <div class="form-group">
	                        <label>Amount</label>
	                        <input id="amount" type="text" name="amount" placeholder="Amount" class="form-control">
	                        <p id="text-amount" class="text-helper text-danger"></p>
	                    </div>

	                    <div class="form-group">
	                        <label>PIN Authenticator</label>
	                        <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
	                        <p id="text-password" class="text-helper text-danger"></p>
	                    </div>

		                <div class="ln_solid"></div>
		                <div class="text-right" id="action">
		                  <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Send</button>
		                  <button id="btn_clear" class="btn btn-danger rounded-0" type="button">Cancel</button>
		                </div>
		                <div class="text-center hidden" id="loader">
		                  <i class="fa fa-spinner fa-spin text-warning"></i>
		                </div>
		            </form>
	            @else
	            	<p>Price : ${{$price}}</p>
	            	<form class="form-horizontal form-label-left" action="{{route('avcoin.sendAdmin')}}" method="POST">
		                @csrf
	                    <div class="form-group">
	                        <label>Address</label>
	                        <input id="address" type="text" name="address" placeholder="Address" class="form-control">
	                        <p id="text-address" class="text-helper text-danger"></p>
	                    </div>

	                    <div class="form-group">
	                        <label>Amount</label>
	                        <input id="amount" type="text" name="amount" placeholder="Amount" class="form-control">
	                        <p id="text-amount" class="text-helper text-danger"></p>
	                    </div>

	                    <div class="form-group">
	                        <label>PIN Authenticator</label>
	                        <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
	                        <p id="text-password" class="text-helper text-danger"></p>
	                    </div>

		                <div class="ln_solid"></div>
		                <div class="text-right" id="action">
		                  <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Send</button>
		                  <button id="btn_clear" class="btn btn-danger rounded-0" type="button">Cancel</button>
		                </div>
		                <div class="text-center hidden" id="loader">
		                  <i class="fa fa-spinner fa-spin text-warning"></i>
		                </div>
		            </form>
	            @endif
	        </div>
	    </div>
	</div>
@endsection
@section('script')
    <script type="text/javascript">
    	$('#address').on('change', function () {
    		$.ajax({
              type: 'GET',
              url: '{{ route('avcoin.checkAddress') }}',
              data: {q : this.value},
              dataType: 'json',
              success: function(response){
              	console.log(response)
                if(response.username){
                  $('#text-address').html('Username : ' + response.username);
                }else{
                  $('#text-address').html('');
                }
              }
            });
    	});

	    $('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	        $('#address').val('');
	        $('#amount').val('');
	        $('#password').val('');
	    });
    </script>
@endsection
