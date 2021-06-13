@extends('layouts.app')

@section('content')
<div class="middle-box-p bg-boxshadow">
    <div class="row">
        <div class="col-12">
            <!-- Content -->
            <div class="ibox-content password-box">
                <div class="text-center">
                    <img style="width: 150px; margin-bottom: 10px;" src="{{asset('images/logo/hcs.png')}}" alt="logo">
                    <h5 class="mb-10">Forgot password</h5>
                </div>
                @include('layouts.partials.alert')

                <div class="row">
                    <div class="col-12">
                        <!-- Form -->
                        <form class="m-t" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ request()->email }}">
                            <div class="form-group">
                                <input name="username" type="text" class="form-control" placeholder="Username" required="">
                            </div>
                            <div class="form-group">
                                <input name="password" type="password" class="form-control" placeholder="Password" required="">
                            </div>
                            <div class="form-group">
                                <input name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" required="">
                            </div>
                            <button type="submit" class="btn btn-warning register">Reset password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12 text-center">
            <p class="copyright-text-passw">
                <span class="reset_pass text-warning" href="#">{{ config('app.name') }}</span><br>
                <span class="reset_pass" href="#">All Rights Reserved &copy; {{date('Y')}}.</span>
            </p>
        </div>
    </div>
</div>
@endsection
