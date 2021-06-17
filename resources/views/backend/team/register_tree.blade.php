@extends('layouts.backend',['page'=>'team','active'=>'network'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-sitemap"></i> <span>Register Network Tree</span></h4>
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
        <h5 class="text-warning">Form Register</h5>
        <hr>
        <form action="{{route('team.saveUserTree')}}" method="POST" id="form-package">
            @csrf
            <div class="form-group">
                <label>Upline in Network Tree</label>
                <input type="text" readonly class="form-control" value="{{$_GET['username']}}">
                <input id="parent" name="parent" type="text" readonly class="form-control hidden" value="{{$_GET['parent']}}">
            </div>
            <div class="form-group">
                <label>Position</label>
                <input id="position_user" name="position" type="text" readonly placeholder="Position" class="form-control" value="{{$_GET['position']}}">
                <input id="position_in_parent" name="position_in_parent" type="text" readonly placeholder="Position" class="form-control hidden" value="{{$_GET['position_in_parent']}}">
            </div>
            <div class="form-group">
                <label>Plan Type</label>
                <select id="plan_type" name="plan_type" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                <option value="">Choose Plan Type</option>
                <option value="Regular">Regular</option>
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

            {{-- <div class="form-group">
                <label>Sponsor</label>
                <select name="sponsor" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                    <option value="">Choose Sponsor</option>
                    <option value="{{$_GET['parent']}}" selected>{{$_GET['username']}}</option>
                    @foreach($upline as $data)
                        <option value="{{$data->upline_id}}">{{ucfirst($data->upline->username)}}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="form-group">
                <label>Sponsor</label>
                <input name="sponsor" type="text" placeholder="Sponsor" class="form-control">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input name="username" type="text" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input name="email" type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
                <label>Country</label>
                <select id="country" name="country" class="selectpicker" data-style="btn-select-tag" data-live-search="true" style="width: 100%;height: 36px;">
                    <option value="">Choose Country</option>
                </select>
            </div>
            <div class="form-group">
                <label>PIN Authenticator</label>
                <input id="pin" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input name="password" type="password" placeholder="Password" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input name="password_confirmation" type="password" placeholder="Confirm Password" class="form-control">
            </div>

            <div class="ln_solid"></div>
            <div class="text-right" id="action">
              <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
              <button onclick="location.href='{{route('team.network')}}';" class="btn btn-danger rounded-0" type="button">Cancel</button>
            </div>
            <div class="text-center hidden" id="loader">
              <i class="fa fa-spinner fa-spin text-warning"></i>
            </div>
        </form>
      </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    var one,two,three,package,nomimal1,nomimal2;
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
                            composition[i] = "<option value='" + item.id + "' data-one='" + item.one + "' data-two='" + item.two + "' data-three='" + item.three + "'>HCS Wallet " + item.one * 100 + "% & Register Wallet " + item.two * 100 + "%</option>";
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

    $('#btn_clear').on('click',function () {
      $("#package").val("").trigger("change");
      $("#wallet").val("").trigger("change");
      $('#grp_input').addClass('hidden');
      $('#password').val('');
    });

    $('#btn_submit').on('click', function () {
        $('#action').addClass('hidden');
        $('#loader').removeClass('hidden');
    });

    load_country();
        function load_country() {
            $('#country').empty();
            var countries = [];
            $.ajax({
                type: 'GET',
                url: '{{url('countries.json')}}',
                dataType: 'json',
                success: function(data){
                    $('#country').append('<option value="">Choose Country</option>');
                    $.each(data, function(i, item) {
                        countries[i] = "<option value='" + item.country + "'>" + item.country + "</option>";
                    });
                    $('#country').append(countries);
                    $('#country').selectpicker('refresh');
                }
            });
        }
</script>
@endsection
