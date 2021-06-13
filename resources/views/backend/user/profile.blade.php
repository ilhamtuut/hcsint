@extends('layouts.backend',['page'=>'user','active'=>'profile'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-profile-male"></i> <span>Profile</span></h4>
@endsection

@section('content')
	<div class="col-lg-12">
	    @include('layouts.partials.alert')
	</div>
    @if(Auth::user()->name)
		<div class="col-lg-12">
	        <div class="card-- bg-boxshadow mb-30">
	            <div class="ibox-title mb-2">
	                <h5>Data Profile</h5>
	            </div>
	            <hr>
	            <div class="card--body">
	                <div class="row">
	                    <div class="col-lg-3 mb-10">
	                        <div class="text-center">
	                            <img class="ui-img-150 rounded-circle mb-10" src="{{ asset('images/logo/usr.png') }}" alt="profile">
                                <br><a class="btn btn-xs btn-warning rounded-0" href="{{route('user.profile.edit')}}"><i class="fa fa-edit"></i> Edit</a>
	                        </div>
	                    </div>

	                    <div class="col-lg-9">
	                        <div class="row">
	                            <div class="@if(Auth::user()->bank) col-lg-6 @else col-lg-12 @endif">
	                                <div class="row mb-2">
	                                    <div class="col-6 text-muted pr-0">
	                                        <h6 class="font--weigth-300 font-s--14">Nama</h6>
	                                    </div>
	                                    <div class="col-6 pl-0">
	                                        : {{ucfirst(Auth::user()->name)}}
	                                    </div>
	                                </div>

	                                <div class="row mb-2">
	                                    <div class="col-6 text-muted pr-0">
	                                        <h6 class="font--weigth-300 font-s--14">Username</h6>
	                                    </div>
	                                    <div class="col-6 pl-0">
	                                        : {{ucfirst(Auth::user()->username)}}
	                                    </div>
	                                </div>

	                                <div class="row mb-2">
	                                    <div class="col-6 text-muted pr-0">
	                                        <h6 class="font--weigth-300 font-s--14">Email</h6>
	                                    </div>
	                                    <div class="col-6 pl-0">
	                                        : {{Auth::user()->email}}
	                                    </div>
	                                </div>

	                                <!-- Contact Area -->
	                                <div class="row mb-2">
	                                    <div class="col-6 text-muted pr-0">
	                                        <h6 class="font--weigth-300 font-s--14">Phone Number</h6>
	                                    </div>
	                                    <div class="col-6 pl-0">
	                                        : {{(Auth::user()->phone_number) ? Auth::user()->phone_number : '-'}}
	                                    </div>
	                                </div>

                                    <div class="row mb-2">
	                                    <div class="col-6 text-muted pr-0">
	                                        <h6 class="font--weigth-300 font-s--14">Country</h6>
	                                    </div>
	                                    <div class="col-6 pl-0">
	                                        : {{(Auth::user()->country) ? Auth::user()->country : '-'}}
	                                    </div>
	                                </div>

	                                @if(Auth::user()->hasRole('member'))
	                                    <div class="row mb-2">
	                                        <div class="col-6 text-muted pr-0">
	                                            <h6 class="font--weigth-300 font-s--14">Username Sponsor</h6>
	                                        </div>
	                                        <div class="col-6 pl-0">
	                                            : {{(Auth::user()->parent) ? ucfirst(Auth::user()->parent->username) : '-'}}
	                                        </div>
	                                    </div>
	                                @endif
	                            </div>
                                @if(Auth::user()->bank)
	                                <div class="col-lg-6">
	                                    <div class="row mb-2">
	                                        <div class="col-6 text-muted pr-0">
	                                            <h6 class="font--weigth-300 font-s--14">Bank Name</h6>
	                                        </div>
	                                        <div class="col-6 pl-0">
	                                            : {{Auth::user()->bank->bank_name}}
	                                        </div>
	                                    </div>
                                        <div class="row mb-2">
	                                        <div class="col-6 text-muted pr-0">
	                                            <h6 class="font--weigth-300 font-s--14">Account Name</h6>
	                                        </div>
	                                        <div class="col-6 pl-0">
	                                            : {{Auth::user()->bank->account_name}}
	                                        </div>
	                                    </div>
                                        <div class="row mb-2">
	                                        <div class="col-6 text-muted pr-0">
	                                            <h6 class="font--weigth-300 font-s--14">Account Number</h6>
	                                        </div>
	                                        <div class="col-6 pl-0">
	                                            : {{Auth::user()->bank->account_number}}
	                                        </div>
	                                    </div>
                                    </div>
                                @endif
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="col-lg-6 mb-30">
	        <div class="ibox-home bg-boxshadow">
	            <div class="ibox-title mb-2">
	                <h5>Change Login Password</h5>
	            </div>
	            <hr>
	            <!-- Ibox Content -->
	            <div class="ibox-content">
	                <form class="form-horizontal form-label-left" action="{{route('user.updatePassword')}}" method="POST">
	                    @csrf
	                    <div class="form-group">
	                        <label>Current Password</label>
	                        <input name="current_password" type="password" required="required" placeholder="Current Password" class="form-control">
	                    </div>
	                    <div class="form-group">
	                        <label>New Password</label>
	                        <input name="new_password" type="password" required="required" placeholder="New Password" class="form-control">
	                    </div>
	                    <div class="form-group">
	                        <label>Confirm Password</label>
	                        <input name="confirm_password" type="password" required="required" placeholder="Confirm Password" class="form-control">
	                    </div>
	                    <div class="ln_solid"></div>
	                    <button class="btn btn-warning w-100" type="submit"><i class="fa fa-pencil"></i> Save Changes</button>
	                </form>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-6 mb-30">
	        <div class="ibox-home bg-boxshadow">
	            <div class="ibox-title mb-2">
	                <h5>Change PIN Authenticator</h5>
	            </div>
	            <hr>
	            <!-- Ibox Content -->
	            <div class="ibox-content">
	                <form class="form-horizontal form-label-left" action="{{route('user.updatePasswordtrx')}}" method="POST">
	                    @csrf
	                    <div class="form-group">
	                        <label>Current PIN Authenticator</label>
	                        <input name="current_pin_authenticator" type="password" required="required" placeholder="Current PIN Authenticator" class="form-control">
	                    </div>
	                    <div class="form-group">
	                        <label>New PIN Authenticator</label>
	                        <input name="new_pin_authenticator" type="password" required="required" placeholder="New PIN Authenticator" class="form-control">
	                    </div>
	                    <div class="form-group">
	                        <label>Confirm PIN Authenticator</label>
	                        <input name="confirm_pin_authenticator" type="password" required="required" placeholder="Confirm PIN Authenticator" class="form-control">
	                    </div>
	                    <div class="ln_solid"></div>
	                    <button class="btn btn-warning w-100 mb-1" type="submit"><i class="fa fa-pencil"></i> Save Changes</button>
                        <a href="{{route('user.resetPin')}}" class="text-warning">Reset PIN Authenticator?</a>
	                </form>
	            </div>
	        </div>
	    </div>
	@else
	  	<div class="col-lg-12">
	      <!-- Ibox -->
	      <div class="ibox-home bg-boxshadow">
	          <!-- Ibox Content -->
	          <div class="ibox-content">
	            <h5 class="text-warning">Input data profile</h5>
	            <hr>
	            <form class="form-horizontal form-label-left" action="{{route('user.inputData')}}" method="POST">
	                @csrf
	                <div class="form-group mt-2">
	                    <label> Name</label>
	                    <input class="form-control" id="name" name="name" type="text" placeholder="Name">
	                </div>
	                <div class="form-group">
	                    <label> Phone Number</label>
	                    <input class="form-control" id="phone_number" name="phone_number" type="text" placeholder="Phone Number">
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
	@endif
@endsection
@section('script')
    <script type="text/javascript">
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
