@extends('layouts.backend',['page'=>'balance','active'=>'av'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_lifesaver"></i> <span>List AV Member</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
          <form action="{{ route('avcoin.list') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <input name="search" class="form-control" type="text" placeholder="Search Address/Username">
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
                          <th>Address</th>
                          <th class="text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{ucfirst($value->user->username)}}</td>
                              <td>{{$value->address}}</td>
                              <td class="text-right">{{number_format($value->balance,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="4" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td class="text-right">{{number_format($total,2)}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {{$data->render()}}
        </div>
    </div>
</div>
@endsection
