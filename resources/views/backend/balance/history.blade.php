@extends('layouts.backend',['page'=>'balance','active'=>'history'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_wallet"></i> <span>{{str_replace("_", " ", $wallet)}} @if($username) [{{ucfirst($username)}}] @endif</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
          @if($id)
            <form action="{{ route('balance.wallet_member',[str_replace(" ", "_", $wallet),$id]) }}" method="get" id="form-search">
          @else
            <form action="{{ route('balance.wallet', str_replace(" ", "_", $wallet)) }}" method="get" id="form-search">
          @endif
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input name="from_date" class="form-control singledate" type="text" placeholder="From Date">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <input name="to_date" class="form-control singledate" type="text" placeholder="To Date">
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
                          <th>Date</th>
                          <th>Description</th>
                          <th class="text-center">Type</th>
                          <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>
                                @if(preg_match('/Transfer/',$value->description) || preg_match('/Sponsor/',$value->description) && !preg_match('/Sponsor Buy/',$value->description))
                                  @if($value->from_id == Auth::user()->id || $value->from_id == $id)
                                    {{$value->description}} to {{ucfirst($value->to->username)}}
                                  @else
                                    {{$value->description}} from {{ucfirst($value->from->username)}}
                                  @endif
                                @else
                                  {{$value->description}}
                                @endif
                              </td>
                              <td class="text-center">
                                @if($value->type == 'IN')
                                  <span class="badge p-1 badge-success">IN</span>
                                @else
                                  @if($value->to_id == Auth::user()->id && !Auth::user()->hasRole('super_admin'))
                                    <span class="badge p-1 badge-success">IN</span>
                                  @else
                                    <span class="badge p-1 badge-danger">OUT</span>
                                  @endif
                                @endif
                              </td>
                              <td class="text-right">
                                {{number_format($value->amount,2)}}
                              </td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="6" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4">Total</td>
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
