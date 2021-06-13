@extends('layouts.backend',['page'=>'convert','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-exchange"></i> <span>Convert</span></h4>
@endsection

@section('content')
	<div class="col-lg-12">
      @include('layouts.partials.alert')
  	</div>
    <div class="col-lg-12 mb-30">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <!-- Ibox Content -->
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-6"><h5 class="text-warning">Convert Cash Wallet to Register Wallet</h5></div>
                    <div class="col-lg-6 text-right"><a class="btn btn-warning rounded-0" href="{{route('convert.history')}}"><i class="fa fa-history"></i> History</a></div>
                </div>
            <hr>
            <p>Balance (CW) : {{$balance}}</p>
            {{-- <p>Price AV : ${{$price}}</p> --}}
            <form class="form-horizontal form-label-left" action="{{route('convert.send')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="control-label">Amount</label>
                    <input id="amount" name="amount" class="form-control" placeholder="Amount" type="text">
                </div>
			    <div class="form-group @if($fee == 0) hidden @endif">
			        <label class="control-label">Fee ({{$fee*100}})%</label>
			        <input id="fee" class="form-control" readonly placeholder="Fee" type="text">
			    </div>
                <div class="form-group" @if($additional == 0) hidden @endif">
			        <label class="control-label">Additional ({{$additional*100}})%</label>
			        <input id="additional" class="form-control" readonly placeholder="Additional" type="text">
			    </div>
                <div class="form-group">
                    <label class="control-label">Register Wallet</label>
                    <input id="receive" class="form-control" readonly placeholder="Register Wallet" type="text">
                </div>
                {{-- <div class="form-group">
                        <label>PIN Authenticator</label>
                        <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
                        <p id="text-password" class="text-helper text-danger"></p>
                </div> --}}

                @include('backend.withdraw.modal_question')

                <div class="ln_solid"></div>
                <div class="text-right">
                    <button class="btn btn-warning rounded-0" type="button" data-toggle="modal" data-target="#modal-question">Submit</button>
                    <button id="btn_clear" class="btn btn-danger rounded-0" type="button">Cancel</button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
    	var fee = {{$fee}}, exchange = {{$exchange}}, add = {{$additional}};
    	$('#amount').on('keyup change',function () {
    		var amount = $(this).val();
    		var total = amount*exchange;
    		var amountFee = total*fee;
    		var amountAdd = total*add;
    		var receive = (total - amountFee) + amountAdd;
	      	$('#fee').val(parseFloat(amountFee).toFixed(2));
	      	$('#additional').val(parseFloat(amountAdd).toFixed(2));
	      	$('#receive').val(parseFloat(receive).toFixed(2));
    	});

    	$('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	      $('#amount').val('');
	      $('#coin').val('');
	      $('#password').val('');
	    });
    </script>
@endsection
