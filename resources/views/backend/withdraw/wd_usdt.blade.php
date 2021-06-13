@extends('layouts.backend',['page'=>'withdraw','active'=>'usdt'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_currency"></i> <span>USDt</span></h4>
@endsection

@section('content')
	<div class="col-lg-12">
      @include('layouts.partials.alert')
  	</div>
  	<div class="col-lg-12">
      <!-- Ibox -->
      <div class="ibox-home bg-boxshadow">
          <!-- Ibox Content -->
          <div class="ibox-content">
            <h5 class="text-warning">Sell CW to USDt</h5>
            <p>Balance (CW) : {{number_format($balance,2)}}</p>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('withdraw.sendUsdt')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="control-label">USDt Address</label>
                    <input id="usdt_address" name="usdt_address" class="form-control" placeholder="USDt Address" type="text">
                </div>
                <div class="form-group">
                    <label class="control-label">Amount</label>
                    <input id="amount" name="amount" class="form-control" placeholder="Amount" type="text">
                </div>
                <div class="form-group">
                    <label class="control-label">Fee ({{$fee * 100}})%</label>
                    <input id="fee" class="form-control" readonly placeholder="Fee" type="text">
                </div>
                <div class="form-group">
                    <label class="control-label">USDt</label>
                    <input id="idr" class="form-control" readonly placeholder="USDt" type="text">
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
    	var price = {{$kurs}},fee = {{$fee}};
    	$('#amount').on('keyup change',function () {
    		var amount = $(this).val();
    		var total = amount * price;
    		var amount_fee = total * fee;
    		var receive = total - amount_fee;
	      	$('#fee').val(parseFloat(amount_fee).toFixed(2));
	      	$('#idr').val(parseFloat(receive).toFixed(2));
    	});

    	$('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	      $('#usdt_address').val('');
	      $('#amount').val('');
	      $('#fee').val('');
	      $('#idr').val('');
	      $('#password').val('');
	    });
    </script>
@endsection
