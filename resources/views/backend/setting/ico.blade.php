@extends('layouts.backend',['active'=>'ico','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-gears"></i> <span>Setting Ico</span></h4>
@endsection

@section('content')
  <div class="col-12">
    @include('layouts.partials.alert')
  </div>
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                          <th width="3%">No</th>
                          <th>Name</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Price</th>
                          <th class="text-right">Min Buy</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $h)
                          <tr>
                              <td>{{++$key}}</td>
                              <td>{{$h->name}}</td>
                              <td class="text-right">{{number_format($h->amount)}}</td>
                              <td class="text-right">{{$h->price}}</td>
                              <td class="text-right">{{$h->min_buy}}</td>
                              <td class="text-center">
                                  <button class="btn btn-xs btn-warning rounded-0 call_modal" data-id="{{$h->id}}" data-amount="{{$h->amount}}" data-price="{{$h->price}}" data-name="{{$h->name}}" data-min_buy="{{$h->min_buy}}" data-toggle="modal" data-target="#responsive-modal" type="button">Update</button>
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
            <form action="{{ route('setting.updateIco') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="col-form-label">Name</label>
                        <input id="name" type="text" readonly class="form-control" placeholder="Name">
                        <input id="id_package" type="text" name="id" class="form-control form-control-sm hidden" placeholder="Percent">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Amount</label>
                        <input id="amount" type="text" name="amount" class="form-control" placeholder="Amount">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Price</label>
                        <input id="price" type="text" name="price" class="form-control" placeholder="Price">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Min Buy</label>
                        <input id="min_buy" type="text" name="min_buy" class="form-control" placeholder="Min Buy">
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
            $('#id_package').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#amount').val($(this).data('amount'));
            $('#price').val($(this).data('price'));
            $('#min_buy').val($(this).data('min_buy'));
        });
        $('#btn_submit').on('click',function () {
          $('#action').addClass('hidden');
          $('#loader').removeClass('hidden');
        });
    });
</script>
@endsection

