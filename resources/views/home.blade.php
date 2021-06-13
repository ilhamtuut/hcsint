@extends('layouts.backend',['page'=>'home'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_house_alt"></i> <span>Dashboard</span></h4>
@endsection

@section('content')
    @if(!Auth::user()->name)
    <div class="col-lg-12">
        <div class="alert alert-info" role="alert">
            Please complete the data first, <a class="text-muted" href="{{route('user.profile')}}">Click here</a>
        </div>
    </div>
    @endif
    <div class="col-lg-12">
	    @include('layouts.partials.alert')
	</div>

    <div class="col-md-4 col-lg-4">
        <!-- Widget Content -->
        <div class="widget-content-style two mb-30">
            <div class="row">
                <div class="col-4">
                    <!-- Icon -->
                    <div class="widget-style-two-icon">
                        <i class="icon_wallet fa-4x" style="color: #1e6977"></i>
                    </div>
                </div>
                <div class="col-8 text-right">
                    <!-- Text -->
                    <div class="widget-style-two-text">
                        <p><a class="text-warning" href="#">HCS Wallet</a></p>
                        <h2 class="widget-content--text mb-5" style="color: #1e6977">${{$hcs}}</h2>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-outline-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('transfer.wallet','hcs_wallet')}}">Transfer</a>
                                <a class="dropdown-item" href="{{route('balance.wallet','HCS_Wallet')}}">History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-4">
        <!-- Widget Content -->
        <div class="widget-content-style two mb-30">
            <div class="row">
                <div class="col-4">
                    <!-- Icon -->
                    <div class="widget-style-two-icon">
                        <i class="icon_wallet fa-4x" style="color: #1e6977"></i>
                    </div>
                </div>
                <div class="col-8 text-right">
                    <!-- Text -->
                    <div class="widget-style-two-text">
                        <p><a class="text-warning" href="#">Register Wallet</a>
                        <h2 class="widget-content--text mb-5" style="color: #1e6977">{{$register}}</h2>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-outline-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('transfer.wallet','register_wallet')}}">Transfer</a>
                                <a class="dropdown-item" href="{{route('balance.wallet','Register_Wallet')}}">History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-4">
        <!-- Widget Content -->
        <div class="widget-content-style two mb-30">
            <div class="row">
                <div class="col-4">
                    <!-- Icon -->
                    <div class="widget-style-two-icon">
                        <i class="icon_wallet fa-4x" style="color: #1e6977"></i>
                    </div>
                </div>
                <div class="col-8 text-right">
                    <!-- Text -->
                    <div class="widget-style-two-text">
                        <p><a class="text-warning" href="#">Cash Wallet</a> </p>
                        <h2 class="widget-content--text mb-5" style="color: #1e6977">${{$cash}}</h2>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-outline-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                            <div class="dropdown-menu">
                                @if(Auth::user()->hasRole('member'))
                                    <a class="dropdown-item" href="{{route('convert.index')}}">Convert</a>
                                    <a class="dropdown-item" href="{{route('withdraw.index')}}">Withdraw</a>
                                @endif
                                <a class="dropdown-item" href="{{route('balance.wallet','Cash_Wallet')}}">History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-12 mb-30">
        <div class="widget-content-style rounded-0 ibox-home">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="widget---stats d-flex align-items-center">
                        <div class="widget---content-text">
                            <h6>Latest Package</h6>
                            <p class="mb-0">{{$package}}</p>
                        </div>
                    </div>
                    <div class="progress progress-small-- ml-0">
                        <div class="progress-bar bg-warning w-100"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget---stats d-flex align-items-center">
                        <div class="widget---content-text">
                            <h6>Total Maximum Profit</h6>
                            <p class="mb-0">${{$max_profit}}</p>
                        </div>
                    </div>
                    <div class="progress progress-small-- ml-0">
                        <div class="progress-bar bg-warning w-100"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget---stats d-flex align-items-center">
                        <div class="widget---content-text">
                            <h6>Today Profit</h6>
                            <p class="mb-0">${{$todayEarn}}</p>
                        </div>
                    </div>
                    <div class="progress progress-small-- ml-0">
                        <div class="progress-bar bg-warning w-100"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget---stats d-flex align-items-center">
                        <div class="widget---content-text">
                            <h6>Total Profit</h6>
                            <p class="mb-0">${{$totalEarn}}</p>
                        </div>
                    </div>
                    <div class="progress progress-small-- ml-0">
                        <div class="progress-bar bg-warning w-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
