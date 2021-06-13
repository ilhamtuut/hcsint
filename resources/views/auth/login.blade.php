@extends('layouts.app')

@section('content')
<div class="middle-box-p bg-boxshadow text-center">
    <img style="width: 150px; margin-bottom: 10px;" src="{{asset('images/logo/hcs.png')}}" alt="logo">
    <h5>Login</h5>
    <div class="text-left">
        @include('layouts.partials.alert')
    </div>

    <form class="#" method="POST" action="{{ route('login') }}" id="form-action">
        @csrf
        <!-- Form Group -->
        <div class="form-group">
            <input id="username" name="username" type="text" class="form-control" placeholder="User Name" required="">
            <p class="text-danger text-left hidden" id="text-error-username">The username is required.</p>
        </div>

        <!-- Form Group -->
        <div class="form-group">
            <input id="password" name="password" type="password" class="form-control" placeholder="Password" required="">
            <p class="text-danger text-left hidden" id="text-error-password">The password is required.</p>
        </div>

        <button type="button" onclick="openModal();" id="btn-action" class="btn btn-warning register"><i class="fa fa-sign-in"></i> Login</button>

        <a class="forgot_pass text-warning" href="{{ route('password.request') }}"><small><i class="fa fa-lock"></i> Forgot Your Password?</small></a>
        <p class="text-center"><small>Don't have account? <a class="text-warning" href="{{ route('register') }}"><i class="fa fa-user"></i> Create Account</a></small></p>
    </form>
</div>
@endsection
