@extends('layouts.backend',['page'=>'','active'=>''])

@section('header')
  <h4 class="font-color-purple"><i class="icon_question_alt2"></i> <span>Secret Question</span></h4>
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
            <h5 class="text-warning">Secret Question</h5>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('question.answer')}}" method="POST">
                @csrf
                <div class="form-group mt-2">
                    <label> Question</label>
                    <select name="question" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                        <option value="">Select Question</option>
                        @foreach ($data as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label> Answer</label>
                    <input class="form-control" name="answer" type="text" placeholder="Answer">
                    <input class="form-control hidden" name="link" value="{{session('link')}}" type="text">
                </div>
                <div class="ln_solid"></div>
                <div class="text-right" id="action">
                    <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
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
	    $('#btn_submit').on('click',function () {
	        $('#action').addClass('hidden');
	        $('#loader').removeClass('hidden');
	    });
    </script>
@endsection
