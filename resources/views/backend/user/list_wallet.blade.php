@extends('layouts.backend',['page'=>'user','active'=>'list_wallet'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_gift_alt"></i> <span>List Address AV</span></h4>
@endsection

@section('content')
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
          <form action="{{ route('user.list_wallet') }}" method="get" id="form-search">
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
                          <th>Address</th>
                          <th class="text-right">Balance</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($data as $h)
                          <tr>
                            <td>{{++$i}}</td>
                            <td>{{ucfirst($h->user->username)}}</td>
                            <td>{{$h->address}}</td>
                            <td class="text-right">{{number_format($h->balance,2)}}</td>
                          </tr>
                        @empty
                            <tr>
                              <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                        @endforelse
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
