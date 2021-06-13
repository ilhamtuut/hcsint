@extends('layouts.backend',['page'=>'user','active'=>'create'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-profile-male"></i> <span>Edit User</span></h4>
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
            <h5 class="text-warning">Input data</h5>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('user.updateData',$user->id)}}" method="POST">
                @csrf
                <div class="form-group mt-2">
                    <label> Username</label>
                    <input class="form-control" id="username" readonly value="{{ucfirst($user->username)}}" type="text" placeholder="User Name">
                </div>
                <div class="form-group mt-2">
                    <label> Name</label>
                    <input class="form-control" id="name" name="name" value="{{ucfirst($user->name)}}" type="text" placeholder="Name">
                </div>
                <div class="form-group mt-2">
                    <label> Email</label>
                    <input class="form-control" id="email" name="email" value="{{$user->email}}" type="text" placeholder="Email">
                </div>
                <div class="form-group">
                    <label> Phone Number</label>
                    <input class="form-control" id="phone_number" name="phone_number" value="{{$user->phone_number}}" type="text" placeholder="Phone Number">
                </div>
                <div class="form-group">
  			          <label>Role</label>
  			          <select id="role" name="role" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
  			            <option value="">Choose Role</option>
  			            @foreach ($roles as $role)
                        @if(Auth::user()->hasRole('admin'))
                            @if($role->id == 3)
                                <option 
                                    value="{{$role->id}}"
                                    @foreach ($user->roles as $r)
                                        @if ($role->id == $r->id)
                                            selected 
                                        @endif
                                    @endforeach
                                >
                                    {{$role->display_name}}
                                </option>
                            @endif
                        @else
                            <option 
                                value="{{$role->id}}"
                                @foreach ($user->roles as $r)
                                    @if ($role->id == $r->id)
                                        selected 
                                    @endif
                                @endforeach
                            >
                                {{$role->display_name}}
                            </option>

                        @endif
                    @endforeach
  			          </select>
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
    	$('#sponsor').keyup(function(e){
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
	        $('#sponsor').val(fullname);
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