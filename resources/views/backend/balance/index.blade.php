@extends('layouts.backend',['page'=>'balance','active'=>'wallet'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_wallet"></i> <span>Balance Wallet</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
          <form action="{{ route('balance.index') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-4"></div>
              <div class="col-md-4">
                <div class="form-group">
                    <select name="wallet" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        <option value="">Choose Wallet</option>
                        @foreach($type as $value)
                          <option value="{{$value->description}}">{{$value->description}}</option>
                        @endforeach
                    </select>
                  </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input name="search" class="form-control" type="text" placeholder="Search">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                          <th width="3%">#</th>
                          <th>Username</th>
                          <th>Description</th>
                          <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>
                                <a class="text-warning" href="{{ route('balance.wallet_member',[str_replace(" ", "_", $value->description),$value->user_id]) }}">{{ucfirst($value->user->username)}}</a>
                              </td>
                              <td>{{$value->description}}</td>
                              <td class="text-right">{{number_format($value->balance,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{$data->render()}}
        </div>
    </div>
</div>
@endsection
