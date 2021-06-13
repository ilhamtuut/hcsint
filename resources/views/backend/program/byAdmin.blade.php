@extends('layouts.backend',['page'=>'program','active'=>'by_admin'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_archive_alt"></i> <span>Invest by Admin</span></h4>
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
            <h5 class="text-warning">Invest</h5>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('program.register_byadmin')}}" method="POST">
                @csrf
			      	<div class="form-group">
				        <label class="control-label">Username</label>
				        <input id="username" name="username" class="form-control" required="required" placeholder="Username" type="text">
				        <ul class="list-gpfrm" id="hdTuto_search"></ul>
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
                      <label>PIN Authenticator</label>
                      <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
                      <p id="text-password" class="text-helper text-danger"></p>
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
@endsection
@section('script')
    <script type="text/javascript">
	    $('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });

	    $('#btn_clear').on('click',function () {
	      $("#package").val("").trigger("change");
	      $('#username').val('');
	      $('#password').val('');
	    });

	    $(document).ready(function(){
            $('#username').keyup(function(e){
                e.preventDefault();
                if(this.value == ''){
                    $('#hdTuto_search').hide();
                }else{
                    $.ajax({
                    type: 'GET',
                    url: '{{ route('user.get_user') }}',
                    data: {search : this.value},
                    dataType: 'json',
                    success: function(response){
                        if(response.error){
                        }else{
                        $('#hdTuto_search').show().html(response.data);
                        }
                    }
                    });
                }
            });

            $(document).on('click', '.list-gpfrm-list', function(e){
                e.preventDefault();
                $('#hdTuto_search').hide();
                var fullname = $(this).data('fullname');
                var id = $(this).data('id');
                $('#username').val(fullname);
            });

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
                $.ajax({
                    url: "{{url('program/plan/')}}/"+type,
                    type: "GET",
                    contentType: "application/json",
                    success: function (data) {
                        if(data.success){
                            $.each(data.plan, function (i,item) {
                                plan[i] = "<option value='" + item.id + "' data-amount='" + item.amount + "'>" + addCommas(item.amount)  + "</option>";
                            });
                            $('#package').append(plan);
                            $('#package').selectpicker('refresh');
                        }
                    },
                    cache: false
                });
            }
        });
    </script>
@endsection
