@extends('layouts.backend',['active'=>'members','page'=>'user'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-profile-male"></i> <span>Sponsor Member</span></h4>
@endsection

@section('content')
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-10">
          <div class="row">
            <div class="col-md-8">
              <h5>
                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin')) 
                  <a class="font-color-purple" href="{{ (Auth::user()->id == $id) ? '#' : route('user.list_donwline_user',\App\Models\User::where('id',$id)->first()->parent->id) }}">{{ucfirst($username)}}</a> 
                @else 
                  <a class="font-color-purple" href="{{ (Auth::user()->id == $id) ? '#' : route('user.list_donwline_user',\App\Models\User::where('id',$id)->first()->parent->id) }}">{{ucfirst($username)}}</a> 
                @endif
              </h5>
            </div>
            <div class="col-md-4">
              @if($id)
                  <form action="{{ route('user.list_donwline_user',$id) }}" method="get" id="form-search">
              @else
                  <form action="{{ route('user.list_donwline') }}" method="get" id="form-search">
              @endif
                <div class="form-group">
                    <div class="input-group">
                        <input name="search" class="form-control" type="text" placeholder="Search Username" required>
                        <span class="input-group-append"> 
                            <button type="button" onclick="submit();" class="btn btn-warning"><i class="fa fa-search"></i></button> 
                        </span>
                    </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                          <th width="3%">No</th>
                          <th>Username</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone Number</th>
                          <th class="text-center">Date Join</th>
                          <th class="text-right">Total Program</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @forelse ($data as $value)
                            @php
                                $total += $value->program->sum('amount');
                            @endphp
                            <tr>
                                <td>{{++$i}}</td>
                                <td><a class="font-color-purple" href="{{route('user.list_donwline_user',$value->id)}}">{{ucfirst($value->username)}}</a></td>
                                <td>{{($value->name)? ucfirst($value->name) :'-'}}</td>
                                <td>{{($value->email)}}</td>
                                <td>{{($value->phone_number)? $value->phone_number :'-'}}</td>
                                <td class="text-center">{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                                <td class="text-right">{{(number_format($value->program->sum('amount'),2))}}</td>
                            </tr>
                        @empty
                            <tr>
                              <td colspan="7" class="text-center">No data available in table</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-blue">
                     <tr>
                       <td colspan="6">Total</td>
                       <td class="text-right">{{number_format($total,2)}}</td>
                     </tr>
                   </tfoot>
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