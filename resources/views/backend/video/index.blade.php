@extends('layouts.backend',['active'=>'video','page'=>'setting'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-video"></i> <span>Asset Crypto</span></h4>
@endsection

@section('content')
  <div class="col-12">
    @include('layouts.partials.alert')
  </div>
  <div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <button class="btn btn-xs btn-warning rounded-0 call_modal mb-5" data-type="add" data-url="{{route('video.store')}}" data-toggle="modal" data-target="#responsive-modal" type="button"><i class="fa fa-plus"></i> Add Video/Image</button>
        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                          <th width="3%">No</th>
                          <th>Title</th>
                          <th>Decription</th>
                          <th>Filename</th>
                          <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($data->count()>0)
                          @foreach ($data as $key => $h)
                            <tr>
                                <td>{{++$i}}</td>
                                <td>{{$h->title}}</td>
                                <td>{{$h->description}}</td>
                                <td>{{$h->filename}}</td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-info rounded-0 video_modal" data-link="{{$h->link_video}}" data-type="{{$h->type}}" data-toggle="modal" data-target="#show-modal" type="button"><i class="fa fa-eye"></i> Show</button>
                                    <button class="btn btn-xs btn-warning rounded-0 call_modal" data-type="update" data-url="{{route('video.update',$h->id)}}" data-title="{{$h->title}}" data-description="{{$h->description}}" data-filename="{{$h->filename}}" data-toggle="modal" data-target="#responsive-modal" type="button"><i class="fa fa-edit"></i> Update</button>
                                    <a class="btn btn-xs btn-danger rounded-0" href="{{route('video.delete',$h->id)}}"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                          @endforeach
                        @else
                            <tr>
                              <td colspan="5" class="text-center">No data available in table</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $data->render() !!}
        </div>
    </div>
  </div>

  <div class="modal fade" id="show-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #222;">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div id="file"></div>
            </div>
        </div>
    </div>
  </div>
  <div class="modal fade" id="responsive-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-white" id="modal-title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="form-video" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-form-label">Title</label>
                        <input id="title" type="text" name="title" class="form-control" placeholder="Title">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Video/Image</label>
                        <input type="file" name="file" class="form-control" placeholder="Video/Image" accept="image/*,video/*">
                        <p class="text-danger">Maximum Video/Image 20MB</p>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Description</label>
                        <textarea name="description" class="form-control" id="description" cols="15" rows="5" placeholder="Description"></textarea>
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
    $('.call_modal').on('click',function(){
        $('#form-video').attr('action',$(this).data('url'));
        if($(this).data('type') == 'update'){
            $('#modal-title').html('Update Video');
            $('#title').val($(this).data('title'));
            $('#description').val($(this).data('description'));
        }else{
            $('#modal-title').html('Add Video');
            $('#title').val('');
            $('#description').val('');
        }
    });
    $('.video_modal').on('click',function(){
        var link = $(this).data('link');
        var type = $(this).data('type');
        $('#file').empty();
        if(type == 'image'){
            $('#file').append('<image id="playImg" style="width: 100%" src="'+link+'"></image>');
        }else{
            $('#file').append('<video id="playVideo" controls style="width: 100%" src="'+link+'"></video>');
        }
    });

    $('#btn_submit').on('click',function () {
        $('#action').addClass('hidden');
        $('#loader').removeClass('hidden');
    });
</script>
@endsection

