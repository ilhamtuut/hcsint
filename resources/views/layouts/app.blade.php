
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
    <meta charset="UTF-8">
    <meta name="description" content="{{ config('app.name') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset('images/logo/icon.png')}}">
    <link rel="stylesheet" href="{{asset('dist/css/plugins/select-tags-css/bootstrap-select.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/plugins/select-tags-css/bootstrap-select-2.css')}}">
    <link rel="stylesheet" href="{{asset('dist/style.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('dist/captcha/slidercaptcha.css')}}" />
</head>

<body>

    <div class="page-wrapper bg-img">
    {{-- <div class="page-wrapper bg-img" style="background-blend-mode: overlay;background: rgba(0, 0, 0, 0.5);background-repeat: no-repeat; background-size: cover; background-position: center center; background-attachment: fixed; background-image: url('{{asset('images/bg-nos.jpg')}}');height: 100%;"> --}}
        <!-- Wrapper -->
        <div class="wrapper wrapper-content---">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Middle Box -->
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_capctha" role="dialog" tabindex="-5"aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" style="background-color: transparent; border: unset; box-shadow: unset;">
            <div class="modal-body">
                <div class="slidercaptcha" style="background-color: #d7d9de;">
                    <div class="captcha-heading">
                        <span class="captcha-title">Drag To Verify</span>
                    </div>
                    <div class="panel-body"><div id="captcha"></div></div>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- jQuery 2.2.4 -->
    <script src="{{asset('dist/assets/js/jquery/jquery.2.2.4.min.js')}}"></script>
    <script src="{{asset('dist/assets/js/bootstrap/popper.min.js')}}"></script>
    <script src="{{asset('dist/assets/js/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('dist/js/plugins-js/select-tags-js/bootstrap-select.js')}}"></script>
    <script src="{{asset('dist/js/plugins-js/select-tags-js/bootstrap-select-2.js')}}"></script>
    <script src="{{asset('dist/js/active.js')}}"></script>
    <script src="{{asset('dist/captcha/longbow.slidercaptcha.js')}}"></script>
    @yield('script')
    <script type="text/javascript">
            var captcha = 0;
            function openModal() {
                $('#text-error-username').addClass('hidden');
                $('#text-error-password').addClass('hidden');
                if($('#username').val() == ''){
                    $('#text-error-username').removeClass('hidden');
                }
                if($('#password').val() == ''){
                    $('#text-error-password').removeClass('hidden');
                }

                if($('#username').val() && $('#password').val()){
                    $('#modal_capctha').modal('show');
                }

            }

            $('#captcha').sliderCaptcha({
                repeatIcon: 'fa fa-redo',
                onSuccess: function () {
                    $('#modal_capctha').modal('hide');
                    $('#btn-action').attr('disabled','disabled');
                    $('#btn-action').html('<i class="fa fa-spinner fa-spin"></i>');
                    captcha = 1;
                    onSubmit();
                }
            });

            function onSubmit() {
                if(captcha == 1){
                    $('#form-action').submit();
                }
            }
        </script>
</body>
</html>
