@extends('layouts.app')

@section('content')
<div class="middle-box-p bg-boxshadow">
    <div class="row">
        <div class="col-12">
            <!-- Content -->
            <div class="ibox-content password-box">
                <div class="text-center">
                    <img style="width: 150px; margin-bottom: 10px;" src="{{asset('images/logo/hcs.png')}}" alt="logo">
                    <h5 class="text-white mb-10">{{ __('Confirm Password') }}</h5>
                </div>
                <p>{{ __('Please confirm your password before continuing.') }}</p>
                @include('layouts.partials.alert')

                <div class="row">
                    <div class="col-12">
                        <form class="m-t" method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="form-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm Password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
