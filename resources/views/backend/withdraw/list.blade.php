@extends('layouts.backend',['page'=>'withdraw','active'=>$type])

@section('header')
  <h4 class="font-color-purple"><i class="icon_currency"></i> <span>List Sell CW {{strtoupper($type)}}</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('withdraw.list',$type) }}" method="get" id="form-search">
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
                  <option @if(request()->status == 1) selected @endif value="1">Pending</option>
                  <option @if(request()->status == 2) selected @endif value="2">Success</option>
                  <option @if(request()->status == 3) selected @endif value="3">Canceled</option>
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
                          <th class="text-center">Status</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Receive</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>{{ucfirst($value->user->username)}}</td>
                              <td class="text-center">
                                @if($value->status == 0)
                                  <span class="badge p-1 badge-warning">Pending</span>
                                @elseif($value->status == 1)
                                  <span class="badge p-1 badge-success">Success</span>
                                @elseif($value->status == 2)
                                  <span class="badge p-1 badge-danger">Canceled</span>
                                @endif
                              </td>
                              <td class="text-right">{{number_format($value->amount,2)}}</td>
                              <td class="text-right">{{number_format($value->receive,2)}}</td>
                              <td class="text-center">
                                <span class="badge badge-secondary" data-target=".detail-modal-{{$value->id}}" data-toggle="modal">Detail</span>
                                <div class="text-left">
                                  @include('backend.withdraw.modal_detail_withdraw', ['wd' => $value])
                                </div>
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
                        <td colspan="4">Total</td>
                        <td class="text-right">{{number_format($amount,2)}}</td>
                        <td class="text-right">{{number_format($receive,2)}}</td>
                        <td></td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {!! $data->appends(['status'=>request()->status,'from_date'=>request()->from_date,'to_date'=>request()->to_date,'search'=>request()->search])->render() !!}
        </div>
    </div>
</div>
@include('backend.withdraw.modal_accept')
@endsection
@section('script')
<script type="text/javascript">
  function accept(id,username) {
    $('#modal-accept').modal('show');
    $('#form-accept').attr('action', '{{ url('/withdraw/accept') }}/'+id);
  }

  function reject(id,username) {
    swal({
      title: 'Are you sure?',
      text: "Reject withdrawal with username "+username,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes'
    }, function(isConfirm) {
      if (isConfirm) {
        $.ajax({
            url:'{{ url('/withdraw/reject') }}/'+id,
            type:'GET',
            success:function(data) {
              location.reload();
            },
        });
      }
    });
  }

  $('#btn_submit').on('click',function(){
    $('#action').addClass('hidden');
    $('#spinner').removeClass('hidden');
  });
</script>
@endsection
