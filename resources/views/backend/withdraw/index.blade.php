@extends('layouts.backend',['page'=>'withdraw','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_currency"></i> <span>Bank</span></h4>
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
            <h5 class="text-warning">Sell CW to Bank</h5>
            <p>Balance (CW) : {{number_format($balance,2)}}</p>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('withdraw.send')}}" method="POST">
                @csrf
                <div class="row">
                	<div class="col-lg-6">
                		<div class="form-group">
					        <label>Bank</label>
					        <input id="bank" name="bank" class="form-control" readonly value="{{Auth::user()->bank->bank_name}}" placeholder="Bank Name" type="text">
					        {{-- <select id="bank" name="bank" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
					            <option value="">Choose Bank</option>
					            <option value="BCA">BCA</option>
					            <option value="BRI">BRI</option>
					            <option value="Mandiri">Mandiri</option>
					        </select> --}}
					    </div>
					    <div class="form-group">
					        <label class="control-label">Account Name</label>
					        <input id="account_name" name="account_name" class="form-control" readonly value="{{Auth::user()->bank->account_name}}" placeholder="Account Name" type="text">
					    </div>
					    <div class="form-group">
					        <label class="control-label">Account Number</label>
					        <input id="account_number" name="account_number" class="form-control" readonly value="{{Auth::user()->bank->account_number}}" placeholder="Account Number" type="text">
					    </div>
					    {{-- <div class="form-group">
					        <label class="control-label">NOTE : Fee {{$fee*100}}%</label>
					    </div> --}}
                	</div>
                	<div class="col-lg-6">
                		{{-- <div class="form-group">
					        <label>Wallet</label>
					        <select id="wallet" name="wallet" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
					            <option value="">Choose Wallet</option>
					            <option value="Cash Wallet">Cash Wallet</option>
					            <option value="Selling Bonus AV">Selling Bonus AV</option>
					        </select>
					    </div> --}}
					    <div class="form-group">
					        <label class="control-label">Amount</label>
					        <input id="amount" name="amount" class="form-control" placeholder="Amount" type="text">
					    </div>
					    <div class="form-group">
					        <label class="control-label">Fee ({{$fee * 100}})%</label>
					        <input id="fee" class="form-control" readonly placeholder="Fee" type="text">
					    </div>
					    <div class="form-group">
					        <label class="control-label">IDR</label>
					        <input id="idr" class="form-control" readonly placeholder="IDR" type="text">
					    </div>
		                {{-- <div class="form-group">
		                      <label>PIN Authenticator</label>
		                      <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
		                      <p id="text-password" class="text-helper text-danger"></p>
		                </div> --}}
                	</div>
                </div>
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
	      	$('#fee').val(addCommas(parseFloat(amount_fee).toFixed(2)));
	      	$('#idr').val(addCommas(parseFloat(receive).toFixed(2)));
    	});

    	$('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	    //   $("#bank").val("").trigger("change");
	    //   $("#wallet").val("").trigger("change");
	    //   $('#account_name').val('');
	    //   $('#account_number').val('');
	      $('#amount').val('');
	      $('#fee').val('');
	      $('#idr').val('');
	      $('#password').val('');
	    });
    </script>
@endsection
