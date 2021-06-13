@extends('layouts.backend',['page'=>'convert','active'=>'list_convert'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-exchange"></i> <span>List Convert</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('convert.list') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-lg-4">
                  <div class="form-group">
                      <input name="from_date" class="form-control singledate" type="text" placeholder="From Date">
                  </div>
              </div>
              <div class="col-lg-4">
                  <div class="form-group">
                      <input name="to_date" class="form-control singledate" type="text" placeholder="To Date">
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
                          <th>Date</th>
                          <th>Username</th>
                          <th>Description</th>
                          <th class="text-center">Status</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Exchange</th>
                          <th class="text-right">Fee</th>
                          <th class="text-right">Additional</th>
                          <th class="text-right">Receive</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>{{ucfirst($value->user->username)}}</td>
                              <td>{{$value->description}}</td>
                              <td class="text-center"><span class="badge p-1 badge-success">Success</span></td>
                              <td class="text-right">{{number_format($value->amount,2)}}</td>
                              <td class="text-right">{{$value->price}}</td>
                              <td class="text-right">{{number_format($value->fee,2)}}</td>
                              <td class="text-right">{{number_format($value->additional,2)}}</td>
                              <td class="text-right">{{number_format($value->receive,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="10" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5">Total</td>
                        <td class="text-right">{{number_format($total,2)}}</td>
                        <td colspan="3"></td>
                        <td class="text-right">{{number_format($receive,2)}}</td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {!! $data->appends(['from_date'=>request()->from_date,'to_date'=>request()->to_date,'search'=>request()->search])->render() !!}
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
  function submit() {
    $("#form-search").submit();
  }
</script>
@endsection
