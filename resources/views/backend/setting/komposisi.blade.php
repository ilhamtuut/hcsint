@extends('layouts.backend',['active'=>'price','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-gears"></i> <span>Setting Composition</span></h4>
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
                          <th class="text-right">Composition 1</th>
                          <th class="text-right">Composition 2</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($data->count()>0)
                          @foreach ($data as $key => $h)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{($h->name == 'Register 1') ? 'Regular': 'Networker'}} </td>
                                <td class="text-right">HCS Wallet {{$h->one * 100}}%</td>
                                <td class="text-right">Register Wallet {{$h->two * 100}}%</td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-warning rounded-0 call_modal" data-id="{{$h->id}}" data-name="{{($h->name == 'Register 1') ? 'Regular': 'Networker'}}" data-one="{{ $h->one*100 }}"  data-two="{{ $h->two*100 }}" data-toggle="modal" data-target="#responsive-modal" type="button">Update</button>
                                </td>
                            </tr>
                          @endforeach
                        @else
                            <tr>
                              <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>

  <div class="modal fade" id="responsive-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-white" id="responsive-modal">Update Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ route('setting.updateComposition') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-form-label">Name</label>
                        <input id="name" type="text" readonly class="form-control" placeholder="Name">
                        <input id="id_package" type="text" name="id" class="form-control form-control-sm hidden" placeholder="Percent">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Composition 1</label>
                        <input id="composition1" type="text" name="composition1" class="form-control" placeholder="Composition 1">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Composition 2</label>
                        <input id="composition2" type="text" name="composition2" class="form-control" placeholder="Composition 2">
                    </div>
                    <div class="form-group">
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
            $('#composition1').val($(this).data('one'));
            $('#composition2').val($(this).data('two'));
        });
        $('#btn_submit').on('click',function () {
          $('#action').addClass('hidden');
          $('#loader').removeClass('hidden');
        });
    });
</script>
@endsection

