@extends('layouts.backend',['active'=>'bank','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-bank"></i> <span>Data Bank</span></h4>
@endsection

@section('content')
  <div class="col-12">
    @include('layouts.partials.alert')
  </div>
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('setting.bank') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-8"></div>
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
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                          <th width="3%">No</th>
                          <th>Username</th>
                          <th>Bank Name</th>
                          <th>Account Name</th>
                          <th>Account Number</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $h)
                          <tr>
                                <td>{{++$key}}</td>
                                <td>{{$h->username}}</td>
                                <td>{{$h->bank ? $h->bank->bank_name : '-'}}</td>
                                <td>{{$h->bank ? $h->bank->account_name : '-'}}</td>
                                <td>{{$h->bank ? $h->bank->account_number : '-'}}</td>
                                <td class="text-center">
                                    @if($h->bank)
                                        <button class="btn btn-xs btn-warning rounded-0 call_modal" data-type="update" data-id="{{$h->bank->id}}" data-username="{{$h->username}}" data-bank="{{$h->bank ? $h->bank->bank_name : ''}}" data-number="{{$h->bank ? $h->bank->account_number : ''}}" data-account="{{$h->bank ? $h->bank->account_name : ''}}" data-toggle="modal" data-target="#responsive-modal" type="button">Update</button>
                                    @else
                                        <button class="btn btn-xs btn-info rounded-0 call_modal" data-type="add" data-id="{{$h->id}}" data-username="{{$h->username}}" data-bank="{{$h->bank ? $h->bank->bank_name : ''}}" data-number="{{$h->bank ? $h->bank->account_number : ''}}" data-account="{{$h->bank ? $h->bank->account_name : ''}}" data-toggle="modal" data-target="#responsive-modal" type="button">Add</button>
                                    @endif
                                </td>
                          </tr>
                        @empty
                            <tr>
                              <td colspan="6" class="text-center">No data available in table</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>

  {{-- modal --}}
  <div class="modal fade" id="responsive-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-white" id="responsive-modal">Update Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ route('setting.updateBank') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="col-form-label">Username</label>
                        <input id="name" type="text" readonly class="form-control" placeholder="Name">
                        <input id="bank_id" type="text" name="id" class="form-control form-control-sm hidden" placeholder="Percent">
                        <input id="type" type="text" name="type" class="form-control form-control-sm hidden" placeholder="Type">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Bank Name</label>
                        <select id="bank" name="bank_name" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                            <option value="BCA">BCA</option>
                            {{-- <option value="BRI">BRI</option> --}}
                            {{-- <option value="Mandiri">Mandiri</option> --}}
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Account Name</label>
                        <input id="account" type="text" name="account_name" class="form-control" placeholder="Account Name">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Account Number</label>
                        <input id="number" type="text" name="account_number" class="form-control" placeholder="Account Number">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">PIN Authenticator</label>
                        <input type="password" name="pin_authenticator" class="form-control" placeholder="PIN Authenticator">
                    </div>
                </div>
                <div class="modal-footer">
                  <div class="text-right" id="action">
                    <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
                    <button type="button" class="btn btn-danger rounded-0" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="text-center hidden" id="loader">
                    <i class="fa fa-spinner fa-spin text-warning"></i>
                  </div>
                </div>
            </form>
        </div>
    </div>
  </div>
@endsection
@section('script')
<script type="text/javascript">
    $(function(){
        $('.call_modal').on('click',function(){
            $('#bank_id').val($(this).data('id'));
            $('#name').val($(this).data('username'));
            // $('#bank').val($(this).data('bank'));
            $('#account').val($(this).data('account'));
            $('#number').val($(this).data('number'));
            $('#type').val($(this).data('type'));
        });
        $('#btn_submit').on('click',function () {
          $('#action').addClass('hidden');
          $('#loader').removeClass('hidden');
        });
    });
</script>
@endsection
