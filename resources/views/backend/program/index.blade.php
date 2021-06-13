@extends('layouts.backend',['page'=>'program','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_archive_alt"></i> <span>Buy Package</span></h4>
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
        <h5 class="text-warning">Buy Package</h5>
        <hr>
        <form class="form-horizontal form-label-left" action="{{route('program.register')}}" method="POST">
            @csrf
            <div class="form-group">
                <label>Plan Type</label>
                <select id="plan_type" name="plan_type" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                <option value="">Choose Plan Type</option>
                @if($plan)
                    <option value="Regular">Regular</option>
                @endif
                <option value="Networker">Networker</option>
                </select>
            </div>
            <div class="form-group">
                <label>Amount Plan</label>
                <select id="package" name="package" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                    <option value="">Choose Amount Plan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Composition Wallet</label>
                <select id="wallet" name="wallet" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                    <option value="">Choose Composition Wallet</option>
                </select>
            </div>
            <div class="form-group row hidden" id="grp_input">
                <div class="col-md-12">
                    <div class="row">
                        <div class="hidden" id="input_one">
                            <label>HCS Wallet</label>
                            <input id="wallet_one" type="text" readonly placeholder="HCS Wallet" class="form-control">
                        </div>
                        <div class="hidden" id="input_two">
                            <label>Register Wallet</label>
                            <input id="wallet_two" type="text" readonly placeholder="Register Wallet" class="form-control">
                        </div>
                        <div class="hidden" id="input_three">
                            <label>Cash Wallet</label>
                            <input id="wallet_three" type="text" readonly placeholder="Cash Wallet" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>PIN Authenticator</label>
                <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
                <p id="text-password" class="text-helper text-danger"></p>
            </div>
            <div class="form-group">
                <div class="checkbox i-checks" onclick="confirmCheck('show')"><label> <input id="agree" name="agree" type="checkbox"><i></i>  I Agree to the terms of use. </label></div>
            </div>

            <div class="ln_solid"></div>
            <div class="text-right" id="action">
              <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
              <button id="btn_clear" class="btn btn-danger rounded-0" type="button">Cancel</button>
            </div>
            <div class="text-center hidden" id="loader">
              <i class="fa fa-spinner fa-spin text-warning"></i>
            </div>
        </form>
      </div>
  </div>
</div>

<div class="modal fade" id="modal_info" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="exampleModalCenterTitle">Membership HCS International</h5>
                <button type="button" class="close" onclick="confirmCheck('disagree')" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                    $program = Session::get('program');
                @endphp
                @if($program)
                    <p class="text-dark">Terima Kasih Anda telah berhasil melakukan pembelian paket, dengan detail data dibawah ini :</p>
                    <ol>
                        <li class="text-dark">Username : {{$program->user->username}}</li>
                        <li class="text-dark">Paket : {{$program->package->name}}</li>
                        <li class="text-dark">Jumlah : ${{$program->package->amount}}</li>
                        <li class="text-dark">Tanggal : {{$program->created_at}}</li>
                    </ol>
                @else
                    <p class="text-dark">Dengan menggunakan informasi pada platform HCS International, Anda telah memahami dan menyetujui segala ketentuan sebagai berikut :</p>
                    <ol>
                        <li class="text-dark">- Tahapan registrasi berdasarkan data profil membership yang sebenar-benarnya.</li>
                        <li class="text-dark">- Buy Package membership sesuai dengan ketentuan platform HCS International (marketing plan).</li>
                        <li class="text-dark">- Max. Contract akan berjalan sesuai dengan marketing plan dari platform HCS International dan/atau menyesuaikan dengan kondisi dari seluruh tabung trade yang dapat diakses secara live trade melalui account membership.</li>
                        <li class="text-dark">- Jika kondisi tabung trade mengalami resiko maka Share Profit Trade akan dihentikan dengan pemberitahuan sebelumnya.</li>
                        <li class="text-dark">- Segala kerugian yang ditimbulkan dari resiko di atas akan dialihkan menjadi Asset Value.</li>
                        <li class="text-dark">- Segala kebijakan dari platform HCS International merupakan penyesuaian berdasarkan situasi dan kondisi yang terjadi dengan pemberitahuan sebelumnya.</li>
                    </ol>
                @endif
            </div>
            <div class="modal-footer">
                @if($program)
                    <button type="submit" class="btn btn-success" data-dismiss="modal">OK</button>
                @else
                    <button type="submit" class="btn btn-warning" id="btn_aggre" onclick="confirmCheck('agree')" data-dismiss="modal">Aggre</button>
                    <button type="button" class="btn btn-danger" id="btn_disaggre" onclick="confirmCheck('disagree')" data-dismiss="modal">Disagree</button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script type="text/javascript">
    	var one,two,three,package,nomimal1,nomimal2;
        $("#modal_info").modal('show');
        $('#plan_type').on('change', function() {
	        var value = $(this).val();
            if(value){
                getPlan(value);
            }
        });

        function getPlan(type) {
            var plan = [], composition = [];
            $("#package").selectpicker('val', '');
            $("#package").find('option').remove();
            $('#package').append('<option value="">Choose Plan</option>');
            $("#package").selectpicker("refresh");
            $("#wallet").selectpicker('val', '');
            $("#wallet").find('option').remove();
            $('#wallet').append('<option value="">Choose Composition Wallet</option>');
            $("#wallet").selectpicker("refresh");
            $.ajax({
                url: "{{url('program/plan/')}}/"+type,
                type: "GET",
                contentType: "application/json",
                success: function (data) {
                    if(data.success){
                        $.each(data.plan, function (i,item) {
                            plan[i] = "<option value='" + item.id + "' data-amount='" + item.amount + "'>" + addCommas(item.amount)  + "</option>";
                        });
                        $.each(data.composition, function (i,item) {
                            if(item.id == 1){
                                composition[i] = "<option value='" + item.id + "' data-one='" + item.one + "' data-two='" + item.two + "' data-three='" + item.three + "'>HCS Wallet " + item.one * 100 + "% & Register Wallet " + item.two * 100 + "%</option>";
                            }else if(item.id == 2){
                                composition[i] = "<option value='" + item.id + "' data-one='" + item.one + "' data-two='" + item.two + "' data-three='" + item.three + "'>HCS Wallet " + item.one * 100 + "% & Cash Wallet " + item.two * 100 + "%</option>";
                            }
                        });
                        $('#package').append(plan);
                        $('#package').selectpicker('refresh');
                        $('#wallet').append(composition);
                        $('#wallet').selectpicker('refresh');
                    }
                },
                cache: false
            });
        }

	    $('#package').on('change', function() {
	        var value = $(this).val();
	        if(value != ''){
	            package = $(this).find(':selected').data('amount');
	            var wallet = $('#wallet').val();
	            if(wallet == 1){
                    nomimal1 = (package * one);
                    nomimal2 = (package * two);
                    $('#grp_input').removeClass('hidden');
                    $('#input_two').removeClass('hidden col-md-6');
                    $('#input_one').removeClass('hidden col-md-6 col-md-12');
                    $('#input_two').addClass('col-md-6');
                    $('#input_one').addClass('col-md-6');
                    $('#input_three').addClass('hidden');

                    $('#wallet_one').val(addCommas(parseFloat(nomimal1).toFixed(2)));
                    $('#wallet_two').val(addCommas(parseFloat(nomimal2).toFixed(2)));
                }else if(wallet == 2){
                    nomimal1 = (package * one);
                    nomimal2 = (package * two);
                    $('#input_two').addClass('hidden');
                    $('#grp_input').removeClass('hidden');
                    $('#input_three').removeClass('hidden col-md-6');
                    $('#input_one').removeClass('hidden col-md-6 col-md-12');
                    $('#input_one').addClass('col-md-6');
                    $('#input_three').addClass('col-md-6');

                    $('#wallet_one').val(addCommas(parseFloat(nomimal1).toFixed(2)));
                    $('#wallet_three').val(addCommas(parseFloat(nomimal2).toFixed(2)));
                }
	        }else{
	            $("#wallet").val("").trigger("change");
	            $('#grp_input').addClass('hidden');
	            package = 0;
	        }
	    });

	    $('#wallet').on('change', function() {
	        var value = $(this).val();
	        package = $('#package').find(':selected').data('amount');
	        one = $(this).find(':selected').data('one');
	        two = $(this).find(':selected').data('two');
	        three = $(this).find(':selected').data('three');
	        if(value == 1){
	            nomimal1 = (package * one);
	            nomimal2 = (package * two);
	            $('#grp_input').removeClass('hidden');
	            $('#input_two').removeClass('hidden col-md-6');
	            $('#input_one').removeClass('hidden col-md-6 col-md-12');
	            $('#input_two').addClass('col-md-6');
	            $('#input_one').addClass('col-md-6');
                $('#input_three').addClass('hidden');

	            $('#wallet_one').val(addCommas(parseFloat(nomimal1).toFixed(2)));
	            $('#wallet_two').val(addCommas(parseFloat(nomimal2).toFixed(2)));
            }else if(value == 2){
                nomimal1 = (package * one);
                nomimal2 = (package * two);
                $('#input_two').addClass('hidden');
                $('#grp_input').removeClass('hidden');
                $('#input_three').removeClass('hidden col-md-6');
                $('#input_one').removeClass('hidden col-md-6 col-md-12');
	            $('#input_one').addClass('col-md-6');
                $('#input_three').addClass('col-md-6');

                $('#wallet_one').val(addCommas(parseFloat(nomimal1).toFixed(2)));
                $('#wallet_three').val(addCommas(parseFloat(nomimal2).toFixed(2)));
	        }else{
	            $('#grp_input').addClass('hidden');
	        }
	    });

	    $('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	      $("#package").val("").trigger("change");
	      $("#wallet").val("").trigger("change");
	      $('#grp_input').addClass('hidden');
	      $('#password').val('');
	    });

        function confirmCheck(type) {
            $("#modal_info").modal('show');
            if(type == 'show') return;
            if (type == 'agree'){
                $('#agree').attr('checked',true);
                $('#action button').removeAttr('disabled')
            }else{
                $('#agree').attr('checked',false);
                $('#action button').attr('disabled','disabled')
            }
        }
    </script>
@endsection
