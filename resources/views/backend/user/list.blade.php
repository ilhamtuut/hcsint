@extends('layouts.backend',['page'=>'user','active'=>$role])

@section('header')
  <h4 class="font-color-purple"><i class="icon-profile-male"></i> <span>List {{ucfirst($role)}}</span></h4>
@endsection

@section('content')
<div class="col-lg-12">
    @include('layouts.partials.alert')
</div>
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('user.list',$role) }}" method="get" id="form-search">
            <div class="row">
              <div class="col-lg-6"></div>
              <div class="col-lg-3">
                <select id="status" name="status" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                  <option value="">Choose Status</option>
                  <option @if(request()->status == 1) selected @endif value="1">Active</option>
                  <option @if(request()->status == 2) selected @endif value="2">Suspend</option>
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
                          <th>Username</th>
                          <th>Email</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{ucfirst($value->username)}}</td>
                              <td>{{$value->email}}</td>
                              <td class="text-center">
                                @if($value->status == 1)
                                  <span class="badge p-1 badge-success">Active</span>
                                @elseif($value->status == 2)
                                  <span class="badge p-1 badge-danger">Suspend</span>
                                @endif
                              </td>
                              <td class="text-center">
                                <a href="{{ route('user.block_unclock', $value->id) }}" class="badge p-1 badge-{{($value->status == 2)? 'success': 'danger'}}"><i class="fa fa-ban"></i> {{($value->status == 2)? 'UnBlock': 'Block'}}</a>
                                <a href="{{ route('user.edit', $value->id) }}" class="badge p-1 badge-info"><i class="fa fa-edit"></i> Edit</a>
                                <a href="#" data-target="#bd-user-modal-lg" data-toggle="modal" class="badge p-1 badge-primary call_modal_user" data-sponsor="{{($value->parent)? $value->parent->username: '-'}}" data-username="{{$value->username}}" data-name="{{$value->name}}" data-email="{{$value->email}}" data-phone_number="{{$value->phone_number}}" data-date="{{$value->created_at}}"><i class="fa fa-info-circle"></i> Detail</a>
                                <a href="{{ route('user.list_donwline_user', $value->id) }}" class="badge p-1 badge-danger"><i class="fa fa-eye"></i> Downline</a>
                                <a href="{{ route('user.resetQuestion', $value->id) }}" class="badge p-1 badge-warning"><i class="fa fa-question-circle"></i> Reset Question</a>
                                <div class="text-left">
                                    @include('backend.user.modal_detail_user', ['user' => $value])
                                </div>
                              </td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {!! $data->appends(['status'=>request()->status,'search'=>request()->search])->render() !!}
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
  function submit() {
    $("#form-search").submit();
  }

  $('.call_modal_user').on('click',function(){
    $('#modal_user_sponsor').html($(this).data('sponsor'));
    $('#modal_user_username').html($(this).data('username'));
    $('#modal_user_name').html($(this).data('name'));
    $('#modal_user_email').html($(this).data('email'));
    $('#modal_user_date').html($(this).data('date'));
    $('#modal_user_phone_number').html($(this).data('phone_number'));
    $('#modal_user_bank_name').html($(this).data('bank_name'));
    $('#modal_user_account_number').html($(this).data('account_number'));
    $('#modal_user_account_name').html($(this).data('account_name'));
  });
</script>
@endsection
