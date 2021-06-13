@extends('layouts.backend',['active'=>'question','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_question_alt2"></i> <span>Question</span></h4>
@endsection

@section('content')
  <div class="col-12">
    @include('layouts.partials.alert')
  </div>
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <!-- Ibox Content -->
        <button class="btn btn-xs btn-warning rounded-0 call_modal mb-2" data-url="{{route('question.store')}}" data-toggle="modal" data-target="#responsive-modal" type="button">Add Question</button>
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                          <th width="3%">No</th>
                          <th>Question</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($data->count()>0)
                          @foreach ($data as $key => $h)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{$h->name}}</td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-warning rounded-0 call_modal" data-url="{{route('question.update',$h->id)}}" data-question="{{$h->name}}" data-toggle="modal" data-target="#responsive-modal" type="button">Update</button>
                                </td>
                            </tr>
                          @endforeach
                        @else
                            <tr>
                              <td colspan="3" class="text-center">No data available in table</td>
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
              <h5 class="modal-title text-white" id="responsive-modal">Add or Update Question</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="" method="POST" id="form-q">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-form-label">Question</label>
                        <input id="question" type="text" name="name" class="form-control" placeholder="Question">
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
            $('#question').val('');
            $('#form-q').attr('action',$(this).data('url'));
            $('#question').val($(this).data('question'));
        });

        $('#btn_submit').on('click',function () {
          $('#action').addClass('hidden');
          $('#loader').removeClass('hidden');
        });
    });
</script>
@endsection

