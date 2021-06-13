@extends('layouts.backend',['active'=>'package','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-gears"></i> <span>Setting Package</span></h4>
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
                          <th class="text-right">ROI (%)</th>
                          <th class="text-right">Sponsor (%)</th>
                          <th class="text-right">Pairing (%)</th>
                          <th class="text-right">Max Profit (%)</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $h)
                          <tr>
                              <td>{{++$key}}</td>
                              <td>{{$h->description}} {{number_format($h->amount)}}</td>
                              <td class="text-right">{{number_format($h->amount)}}</td>
                              <td class="text-right">{{$h->roi*100}}</td>
                              <td class="text-right">{{$h->sponsor*100}}</td>
                              <td class="text-right">{{$h->pairing*100}}</td>
                              <td class="text-right">{{$h->max_profit*100}}</td>
                              <td class="text-center">
                                  <button class="btn btn-xs btn-warning rounded-0 call_modal" data-id="{{$h->id}}" data-amount="{{$h->amount}}" data-roi="{{$h->roi*100}}" data-name="{{$h->description." ".number_format($h->amount)}}" data-sponsor="{{$h->sponsor*100}}" data-pairing="{{$h->pairing*100}}" data-max_profit="{{$h->max_profit*100}}" data-toggle="modal" data-target="#responsive-modal" type="button">Update</button>
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
            <form action="{{ route('setting.updatePackage') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="col-form-label">Name</label>
                        <input id="name" type="text" readonly class="form-control" placeholder="Name">
                        <input id="id_package" type="text" name="id" class="form-control form-control-sm hidden" placeholder="Percent">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">ROI</label>
                        <input id="roi" type="text" name="roi" class="form-control" placeholder="ROi">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Sponsor</label>
                        <input id="sponsor" type="text" name="sponsor" class="form-control" placeholder="Sponsor">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Pairing</label>
                        <input id="pairing" type="text" name="pairing" class="form-control" placeholder="Pairing">
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-form-label">Max Profit</label>
                        <input id="max_profit" type="text" name="max_profit" class="form-control" placeholder="Max Profit">
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
            $('#roi').val($(this).data('roi'));
            $('#sponsor').val($(this).data('sponsor'));
            $('#pairing').val($(this).data('pairing'));
            $('#max_profit').val($(this).data('max_profit'));
        });
        $('#btn_submit').on('click',function () {
          $('#action').addClass('hidden');
          $('#loader').removeClass('hidden');
        });
    });
</script>
@endsection
