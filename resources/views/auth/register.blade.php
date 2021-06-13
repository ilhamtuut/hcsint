@extends('layouts.app')

@section('content')
<div class="middle-box-p bg-boxshadow text-center" style="max-width: 450px;">
    <img style="width: 150px; margin-bottom: 10px;" src="{{asset('images/logo/hcs.png')}}" alt="logo">
    <p>Sign Up to Member</p>
    <div class="text-left">
        @include('layouts.partials.alert')
    </div>
    <!-- Form -->
    <form class="#" action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-area text-left">
            <div class="form-group">
                <input name="referral" style="margin:0px;" class="form-control" type="text" required="" @if(Session::get('ref:user:username')) readonly @endif placeholder="Referral" value="{{ Session::get('ref:user:username')}}">
            </div>
            <div class="form-group">
                <input name="username" style="margin:0px;" class="form-control" type="text" required="" placeholder="Username" value="{{ old('username') }}">
            </div>
            {{-- <div class="form-group">
                <input name="name" style="margin:0px;" class="form-control" type="text" required="" placeholder="Name" value="{{ old('name') }}">
            </div> --}}
            <div class="form-group">
                <input name="email" style="margin:0px;" class="form-control" type="text" required="" placeholder="Email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <select id="country" name="country" class="selectpicker" data-style="btn-select-tag" data-live-search="true" style="width: 100%;height: 36px;">
                    <option value="">Choose Country</option>
                  </select>
            </div>
            {{-- <div class="form-group">
                <input name="phone_number" style="margin:0px;" class="form-control" type="text" required="" placeholder="Phone Number" value="{{ old('phone_number') }}">
            </div> --}}
            <div class="form-group">
                <input name="pin_authenticator" style="margin:0px;" class="form-control" type="password" required=" " placeholder="PIN Authenticator">
            </div>
            <div class="form-group">
                <input name="password" style="margin:0px;" class="form-control" type="password" required="" placeholder="Password">
            </div>
            <div class="form-group">
                <input name="password_confirmation" style="margin:0px;" class="form-control" type="password" required="" placeholder="Confirm Password">
            </div>

            <div class="form-group text-left">
                <div class="checkbox i-checks text-white"><label> <input required="" type="checkbox"><i></i>  I Agree to the terms of use. </label></div>
            </div>
            <button type="submit" class="btn btn-warning register">Submit</button>
            <p class="account-desc text-center">Already have an account? <a class="text-warning" href="{{route('login')}}"><i class="fa fa-sign-in"></i> Login</a></p>
        </div>
    </form>
</div>
@endsection
@section('script')
    <script type="text/javascript">
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
