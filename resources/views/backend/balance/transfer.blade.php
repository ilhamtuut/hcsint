@extends('layouts.backend',['page'=>'balance','active'=>'transfer'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_wallet"></i> <span>Transfer {{str_replace('_', ' ', $type)}}</span></h4>
@endsection

@section('content')
  <div class="col-lg-12">
      @include('layouts.partials.alert')
  </div>
  <div class="col-lg-12">
      <!-- Ibox -->
      <div class="ibox-home bg-boxshadow">
          <!-- Ibox Content -->
          <div class="ibox-content">
            <h5 class="text-warning">Transfer {{str_replace('_', ' ', $type)}}</h5>
            <hr>
            <p>Balance : {{number_format($saldo->balance,2)}}</p>
            <form class="form-horizontal form-label-left" action="{{route('transfer.send',$type)}}" method="POST">
                @csrf
                  <div class="form-group mt-2">
                      <label> Username</label>
                      <input class="form-control" id="username" name="username" type="text" placeholder="Username">
                      <p class="text-danger" id="username-error"></p>
                  </div>
                  <div class="form-group hidden" id="usr_show">
                      <label> Recipient Name</label>
                      <input class="form-control" id="name" type="text" placeholder="Recipient Name" readonly>
                      <p class="text-danger" id="name_error"></p>
                  </div>
                  <div class="form-group">
                      <label> Amount</label>
                      <input class="form-control" id="amount" name="amount" type="text" placeholder="Amount">
                      <p class="text-danger" id="amount-error"></p>
                  </div>

                  <div class="form-group">
                      <label>PIN Authenticator</label>
                      <input id="password" name="pin_authenticator" type="password" placeholder="PIN Authenticator" class="form-control">
                      <p id="text-password" class="text-helper text-danger"></p>
                  </div>

                <div class="ln_solid"></div>
                <div class="text-right" id="action">
                  <button id="btn_transfer" class="btn btn-warning rounded-0" type="submit">Send</button>
                  <button id="btn_clear" class="btn btn-danger rounded-0" type="button">Cancel</button>
                </div>
                <div class="text-center hidden" id="loader">
                  <i class="fa fa-spinner fa-spin text-warning"></i>
                </div>
            </form>
          </div>
      </div>
  </div>
@endsection
@section('script')
<script type="text/javascript">
    $('#username').on('keyup',function (e) {
        e.preventDefault();
        $.ajax({
          type: 'GET',
          url: '{{ route('transfer.check') }}',
          data: {username : this.value},
          dataType: 'json',
          success: function(response){
            if(response.success){
              $('#usr_show').removeClass('hidden');
              $('#name').val(response.name);
            }else{
              $('#usr_show').addClass('hidden');
              $('#name').val(''); 
            }
          }
        });
    });

    $('#btn_transfer').on('click',function () {
      $('#action').addClass('hidden');
      $('#loader').removeClass('hidden');
    });

    $('#btn_clear').on('click',function () {
      $('#usr_show').addClass('hidden');
      $('#username').val('');
      $('#amount').val('');
      $('#password').val('');
    });
</script>
@endsection