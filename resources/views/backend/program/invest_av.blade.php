@extends('layouts.backend',['page'=>'program','active'=>'invest_avcoin'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_archive_alt"></i> <span>Register with AV</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('program.list_av') }}" method="get" id="form-search">
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
                          <th>Plan</th>
                          <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>{{ucfirst($value->user->username)}}</td>
                              <td>{{$value->package->name}}</td>
                              <td class="text-right">{{number_format($value->avc,8)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="5" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4">Total</td>
                        <td class="text-right">{{number_format($total,8)}}</td>
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