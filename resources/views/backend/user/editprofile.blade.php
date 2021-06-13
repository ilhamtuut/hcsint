@extends('layouts.backend',['page'=>'user','active'=>'edit_profile'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-profile-male"></i> <span>Edit Profile</span></h4>
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
            <h5 class="text-warning">Edit data profile</h5>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('user.inputData')}}" method="POST">
                @csrf
                <div class="form-group mt-2">
                    <label> Name</label>
                    <input class="form-control" id="name" name="name" value="{{ucfirst(Auth::user()->name)}}" type="text" placeholder="Name">
                </div>
                {{-- <div class="form-group">
                    <label> Email</label>
                    <input class="form-control" id="email" name="email" value="{{ucfirst(Auth::user()->email)}}" type="text" placeholder="Phone Number">
                </div> --}}
                <div class="form-group">
                    <label> Phone Number</label>
                    <input class="form-control" id="phone_number" value="{{ucfirst(Auth::user()->phone_number)}}" name="phone_number" type="text" placeholder="Phone Number">
                </div>
                <div class="ln_solid"></div>
                <div class="text-right" id="action">
                    <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
                    <a href="{{route('user.profile')}}" class="btn btn-danger rounded-0" type="button">Cancel</a>
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
    </script>
@endsection
