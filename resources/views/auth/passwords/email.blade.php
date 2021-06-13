@extends('layouts.app')

@section('content')
<div class="middle-box-p bg-boxshadow">
    <div class="text-center">
        <img style="width: 150px; margin-bottom: 10px;" src="{{asset('images/logo/hcs.png')}}" alt="logo">
        <h5>Forgot Password</h5>
    </div>
    <p>Enter your email will be reset and emailed to you.</p>
    @include('layouts.partials.alert')

    <div class="row">
        <div class="col-12">
            <!-- Form -->
            <form class="m-t" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input name="email" type="text" class="form-control" placeholder="Email" required="">
                </div>
                <button type="submit" class="btn btn-warning register mb-10">Send Password Reset Link</button>
                <p class="copyright-text-passw text-center">
                    <a class="reset_pass text-warning" href="{{route('login')}}">Back to Login.</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
