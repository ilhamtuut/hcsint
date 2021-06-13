@extends('layouts.backend',['page'=>'user','active'=>'list_sponsor'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_gift_alt"></i> <span>List Sponsor</span></h4>
@endsection

@section('content')
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
          <form action="{{ route('user.list_sponsor') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-8"></div>
              <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input name="search" class="form-control" type="text" placeholder="Search Username" required>
                        <span class="input-group-append"> 
                            <button type="button" onclick="submit();" class="btn btn-warning"><i class="fa fa-search"></i></button> 
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
                <table class="table table-striped table-hover">
                      <thead>
                        <tr>
                          <th width="3%">#</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th class="text-right">Total Sponsor</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if($data->count()>0)
                          @foreach ($data as $h)
                            @php
                              $downline = $h->childs()->pluck('id');
                              $totalprogram = \App\Models\Program::whereIn('user_id',$downline)
                                          ->where('registered_by','>',0)
                                          ->sum('amount');
                            @endphp
                              <tr>
                                <td>{{++$i}}</td>
                                <td>{{ucfirst($h->username)}}</td>
                                <td>{{$h->email}}</td>
                                <td class="text-right">{{number_format($totalprogram,2)}}</td>
                              </tr>
                          @endforeach
                        @else
                            <tr>
                              <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                        @endif
                      </tbody>
                  </table>
                  <div class="text-center">
                    {!! $data->render() !!}  
                  </div>
            </div>
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
