@extends('layouts.backend',['page'=>'user','active'=>'bank'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-bank"></i> <span>Input Account Bank</span></h4>
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
            <h6 class="text-warning"><i class="fa fa-edit"></i> Form Input</h6>
            <hr>
            <form class="form-horizontal form-label-left" action="{{route('user.bank.save')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Bank</label>
                    <select id="bank" name="bank_name" style="width: 100%;" class="selectpicker" data-style="btn-select-tag">
                        <option value="">Choose Bank</option>
                        <option value="BCA">BCA</option>
                        {{-- <option value="BRI">BRI</option> --}}
                        {{-- <option value="Mandiri">Mandiri</option> --}}
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Account Name</label>
                    <input id="account_name" name="account_name" class="form-control" placeholder="Account Name" type="text">
                </div>
                <div class="form-group">
                    <label class="control-label">Account Number</label>
                    <input id="account_number" name="account_number" class="form-control" placeholder="Account Number" type="text">
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
