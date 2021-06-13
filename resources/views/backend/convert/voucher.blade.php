@extends('layouts.backend',['page'=>'convert','active'=>'voucher'])

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
                <div class="col-lg-6"><h5 class="text-warning">Convert Cash Wallet to Voucher</h5></div>
                <div class="col-lg-6 text-right"><a class="btn btn-warning rounded-0" href="{{route('convert.history_voucher')}}"><i class="fa fa-history"></i> History</a></div>
            </div>
            <hr>
            <p>Balance (CW) : {{$balance}}</p>
            <form class="form-horizontal form-label-left" action="{{route('convert.sendVoucher')}}" method="POST">
                @csrf
			    <div class="form-group">
			        <label class="control-label">Voucher</label>
                    <select name="voucher" id="voucher" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        <option value="">Choose Voucher</option>
                        @foreach ($vouchers as $item)
                            <optgroup label="{{$item->nama}}">
                                @foreach ($item->paket as $value)
                                    <option value="{{$value->id_paket}}" data-price="{{$value->harga}}" data-name="{{$value->nama_paket}}">{{$value->nama_paket}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
			        <input id="price_voucher" name="price" class="form-control hidden" type="text">
			        <input id="voucher_name" name="voucher_name" class="form-control hidden" type="text">
			    </div>
			    <div class="form-group">
			        <label class="control-label">Cash Wallet</label>
			        <input id="amount" class="form-control" readonly placeholder="Cash Wallet" type="text">
			    </div>
			    <div class="form-group">
			        <label class="control-label">Fee ({{$fee*100}})%</label>
			        <input id="fee" class="form-control" readonly placeholder="Fee" type="text">
			    </div>
			    <div class="form-group">
			        <label class="control-label">Total</label>
			        <input id="total" class="form-control" readonly placeholder="Total" type="text">
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
    	var exchange = {{$exchange}},fee = {{$fee}};
    	$('#voucher').on('change',function () {
    		var paket_id = $(this).val();
            var price = $(this).find(':selected').data('price');
            var name = $(this).find(':selected').data('name');
    		var amount = parseFloat((price/exchange) + 0.01).toFixed(2);
            var amountFee = parseFloat(amount * fee).toFixed(2);
            var total = parseFloat(parseFloat(amount) + parseFloat(amountFee)).toFixed(2);
            $('#price_voucher').val(price);
            $('#voucher_name').val(name);
	      	$('#amount').val(amount);
	      	$('#fee').val(amountFee);
	      	$('#total').val(total);
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
