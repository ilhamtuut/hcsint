@extends('layouts.backend',['page'=>'program','active'=>'history'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_archive_alt"></i> <span>History Buy Package</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('program.history') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-8"></div>
              <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input name="date" class="form-control singledate" type="text" placeholder="Date">
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
                          <th>Plan</th>
                          <th class="text-center">Status</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Max Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                                <td>{{++$i}}</td>
                                <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                                <td>
                                    {{$value->package->description}} {{number_format($value->package->amount)}}
                                    @if($value->package->description == 'Regular')
                                        <span class="badge p-1 badge-primary cursor-pointer" onclick="move_action({{$value->id}})">Move to Networker</span>
                                    @endif
                                </td>
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
                        <td class="text-right">{{number_format($total_max,2)}}</td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {{$data->render()}}
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    function move_action(id) {
        swal({
        title: 'Are you sure?',
        text: "Regular Package move to Networker Package",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes'
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url:'{{ url('/program/move/plan') }}/'+id,
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
