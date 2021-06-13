@extends('layouts.backend',['page'=>'program','active'=>$active])

@section('header')
  <h4 class="font-color-purple"><i class="icon_archive_alt"></i> <span>List Invest by {{ucfirst($regby)}}</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('program.list', $regby) }}" method="get" id="form-search">
            <div class="row">
              <div class="col-lg-3">
                  <div class="form-group">
                      <input name="from_date" class="form-control singledate" type="text" placeholder="From Date">
                  </div>
              </div>
              <div class="col-lg-3">
                  <div class="form-group">
                      <input name="to_date" class="form-control singledate" type="text" placeholder="To Date">
                  </div>
              </div>
              <div class="col-lg-3">
                <select id="status" name="status" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                  <option value="">Choose Status</option>
                  <option @if($status == 2) selected @endif value="2">Completed</option>
                  <option @if($status == 1) selected @endif value="1">On Process</option>
                  <option @if($status == 3) selected @endif value="3">Stop Profit</option>
                </select>
              </div>
              <div class="col-md-3">
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
                          <th>Date</th>
                          <th>Username</th>
                          <th>Plan</th>
                          <th class="text-center">Status</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Max Profit</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>{{ucfirst($value->user->username)}}</td>
                              <td>{{$value->package->description}} {{number_format($value->package->amount)}}</td>
                              <td class="text-center">
                                @if($value->status == 1)
                                  <span class="badge p-1 badge-success">Completed</span>
                                @elseif($value->status == 2)
                                  <span class="badge p-1 badge-danger">Stop Bonus</span>
                                @else
                                  <span class="badge p-1 badge-warning">On Process</span>
                                @endif
                              </td>
                              <td class="text-right">{{number_format($value->amount,2)}}</td>
                              <td class="text-right">{{number_format($value->max_profit,2)}}</td>
                              <td class="text-center">
                                @if($value->status == 0)
                                  <a href="#" onclick="show_action({{$value->id}},'{{ucfirst($value->user->username)}}','profit','stop','Stop Profit');">
                                  <span class="badge p-1 badge-danger">Stop Profit</span></a>
                                @elseif($value->status == 2)
                                  <a href="#" onclick="show_action({{$value->id}},'{{ucfirst($value->user->username)}}','profit','run','Run Profit');">
                                  <span class="badge p-1 badge-success">Run Profit</span></a>
                                @else
                                  <span class="badge p-1 badge-primary">No Action</span>
                                @endif
                              </td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5">Total</td>
                        <td class="text-right">{{number_format($total,2)}}</td>
                        <td class="text-right">{{number_format($total_profit,2)}}</td>
                        <td></td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {!! $data->appends(['status'=>request()->status,'from_date'=>request()->from_date,'to_date'=>request()->to_date,'search'=>request()->search])->render() !!}
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    function submit() {
        $("#form-search").submit();
    }

    function show_action(id,username,type,desc,act) {
        swal({
        title: 'Are you sure?',
        text: act + " with username " + username,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes'
        }, function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url:'{{ url('/program/profit_capital') }}/'+type+'/'+desc+'/'+id,
                type:'GET',
                success:function(data) {
                location.reload();
                },
            });
        }
        });
    }
</script>
@endsection
