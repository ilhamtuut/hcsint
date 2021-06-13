@extends('layouts.backend',['page'=>'setting','active'=>'metatrader'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_datareport"></i> <span>Meta Trader</span></h4>
@endsection

@section('content')
<div class="col-lg-12">
 	@include('layouts.partials.alert')
</div>
<div class="col-lg-12">
  	<!-- Ibox -->
  	<div class="ibox-home bg-boxshadow">
      <!-- Ibox Content -->
      	<div class="row">
            <div class="col-sm-12 col-md-8">
              <a href="#" class="call_modal btn btn-sm btn-success mb-3" data-toggle="modal" data-target="#mt4-modal" data-type="add" data-title="Add Account" data-url="{{route('metatrader.store')}}"><i class="fa fa-plus"></i> Add Account</a>
            </div>
            <div class="col-sm-12 col-md-4">
              <form action="{{ route('metatrader.list') }}" method="get">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm bg-white" aria-label="" aria-describedby="basic-addon1" placeholder="Search AccountID" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-warning cursor-pointer" type="submit">Search</button>
                    </div>
                </div>
              </form>
            </div>
        </div>
      	<div class="ibox-content">
			<div class="table-responsive">
                <table class="table table-hover">
                  <thead class="bg-purple text-white">
                        <tr>
                          <th width="3%">#</th>
                          <th>Name</th>
                          <th>AccountID</th>
                          <th>Password</th>
                          <th>Server</th>
                          <th>Type</th>
                          <th>Nominal</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      @forelse($data as $key => $value)
    						<tr>
    							<td>{{++$i}}</td>
    							<td>{{$value->name}}</td>
    							<td>{{$value->accountID}}</td>
    							<td>{{$value->password}}</td>
                    	      	<td>{{$value->server}}</td>
                        		<td>{{$value->type}}</td>
                        		<td>{{$value->nominal}}</td>
    							<td class="text-center">
		                            <a href="#" class="call_modal btn btn-sm btn-info" data-toggle="modal" data-target="#mt4-modal" data-type="update" data-title="Update Account" data-url="{{route('metatrader.update',$value->id)}}" data-name="{{$value->name}}" data-account_id="{{$value->accountID}}" data-password="{{$value->password}}" data-server="{{$value->server}}" data-type_account="{{$value->type}}" data-nominal="{{$value->nominal}}" data-status_account="{{$value->status}}"><i class="fa fa-edit"></i> Update</a>
		                            <a href="{{route('metatrader.delete',$value->id)}}" class="call_modal btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>
	                          	</td>
    						</tr>
                      @empty
                        <tr>
                          <td colspan="8" class="text-center">Empty data</td>
                        </tr>
                      @endforelse
                    </tbody>
                </table>
                <div class="text-center pt-2">
                  {!! $data->render() !!}
                </div>
            </div>
      	</div>
  	</div>
</div>

<div class="modal fade" id="mt4-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-white" id="title-modal"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="#" method="POST" id="form-mt4">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group m-b-0">
                        <label class="col-form-label">Name</label>
                        <input id="name" type="text" name="name" class="form-control form-control-sm bg-white" placeholder="Name">
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-form-label">AccountID</label>
                        <input id="account_id" type="text" name="accountID" class="form-control form-control-sm" placeholder="AccountID">
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-form-label">Password</label>
                        <input id="password" type="text" name="password" class="form-control form-control-sm" placeholder="Password">
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-form-label">Server</label>
                        <input id="server" type="text" name="server" class="form-control form-control-sm" placeholder="Server">
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-form-label">Type</label>
                        <select id="type_account" name="type" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                          <option value="">Choose Type</option>
                          <option value="Account Cent">Account Cent</option>
                          <option value="Account Regular">Account Regular</option>
                          <option value="Account Standart">Account Standart</option>
                        </select>
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-form-label">Nominal</label>
                        <input id="nominal" type="text" name="nominal" class="form-control form-control-sm" placeholder="Nominal">
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-warning">Submit</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
  $('.call_modal').on('click', function () {
    var url = $(this).data('url');
    var type = $(this).data('type');
    var title = $(this).data('title');
    $('#title-modal').html(title);
    $('#form-mt4').attr("action",url);
    if(type == 'update'){
      var name = $(this).data('name');
      var account_id = $(this).data('account_id');
      var password = $(this).data('password');
      var server = $(this).data('server');
      var nominal = $(this).data('nominal');
      var type_account = $(this).data('type_account');
      var status_account = $(this).data('status_account');
      $('#name').val(name);
      $('#account_id').val(account_id);
      $('#password').val(password);
      $('#server').val(server);
      $('#nominal').val(nominal);
      $('#type_account').val(type_account).trigger('change');
    }else{
      $('#name').val(name);
      clear();
    }
  });

  function clear() {
    $('#name').val('');
    $('#account_id').val('');
    $('#password').val('');
    $('#server').val('');
    $('#nominal').val('');
    $('#type_account').val('');
    $('#status_account').val('');
  }
</script>
@endsection
