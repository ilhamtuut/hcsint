<!DOCTYPE html>
<html lang="en">
    @include('layouts.partials.htmlheader')
<body>

    <div class="page-wrapper">

        <!-- ###### Layout Container Area ###### -->
        <div class="layout-container-area">

            <!-- Side Menu Area -->
            <div class="side-menu-area">

                <div class="logo-bar text-center">
                    <!-- logo -->
                    <a href="{{route('home')}}" class="logo">
                        <span class="big-logo">
                            <img src="{{asset('images/logo/hcs-side.png')}}" alt="">
                        </span>
                        <span class="small-logo">
                            <img src="{{asset('images/logo/icon.png')}}" alt="">
                        </span>
                    </a>
                </div>
                @include('layouts.partials.sidebar')
            </div>
            <!-- Layout Container -->
            <div class="layout-container sidemenu-container">

                <!-- ***** Page Top Bar Area ***** -->
                @include('layouts.partials.topheader')

                <!-- Wrapper -->
                <div class="wrapper wrapper-content mb-30">
                    <div class="container-fluid">
                        <div class="row mb-10">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                @yield('header')
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                                @if(Route::current()->getName() == 'home')
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" id="referal" value="{{route('referal',Auth::user()->username)}}" readonly class="form-control bg-white">
                                            <span class="input-group-append">
                                                <button type="button" onclick="copyReferal();" class="btn btn-warning" style="background-color: #1e6977 !important;"><i class="fa fa-copy text-white"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                @if (Route::current()->getName() == 'avcoin.explorer')
                                    <form action="{{ route('avcoin.search') }}" method="get" id="form-search">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input name="q" class="form-control" type="text" placeholder="Search Txid/Address">
                                                <span class="input-group-append">
                                                    <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                @if (Route::current()->getName() == 'marketplace.index')
                                    <a href="{{route('product.create')}}" class="btn btn-warning rounded-0"><i class="fa fa-plus"></i> Post Product</a>
                                @endif
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Copywrite Text -->
                            <div class="copywrite-text">
                                <p>{{ config('app.name') }} © {{date('Y')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('layouts.partials.footer')
    @include('layouts.partials.script')
    @yield('script')
</body>
</html>
