@extends('layouts.backend',['page'=>'','active'=>''])

@section('header')
  <h4 class="font-color-purple"><i class="icon-hourglass"></i> <span>Count down</span></h4>
@endsection

@section('content')
	<div class="col-12">
        <div class="cooming_soon_content text-center">
            <div class="subscribe_bar wow fadeInUp mb-30" data-wow-delay="1s">
                <h2 class="text-warning"><i class="icon-hourglass"></i> Count down Network</h2>
            </div>
            <div class="coming_soon_timer wow fadeInUp" data-wow-delay="0.6s">
                <div id="clock"></div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript">
	if ($.fn.countdown) {
        $('#clock').countdown('{{$set_date}}', function (event) {
            $(this).html(event.strftime('<div>%D <span>Days</span></div> <div>%H <span>Hours</span></div> <div>%M <span>Minutes</span></div> <div>%S <span>Seconds</span></div>'));
        });
    }
</script>
@endsection
