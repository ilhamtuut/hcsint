@extends('layouts.backend',['page'=>'product','active'=>'category'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_cart_alt"></i> <span>Category</span></h4>
@endsection

@section('content')
    <div class="col-12">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <!-- Ibox Content -->
            @php
                if($type == 'childs'){
                    $parent = \App\Models\ProductCategory::find(request()->id);
                    $url = route('product.category.index');
                    if($parent->parent_id){
                        $url = route('product.category.show',$parent->parent_id);
                    }
                    echo '<button class="btn btn-xs btn-warning rounded-0 category_modal" data-url="'.route('product.category.store').'" data-type="add_child" data-title="Add Childs Category" data-parent="'.$parent->id.'" data-name="'.$parent->name.'" data-toggle="modal" data-target="#category-modal" type="button"><i class="fa fa-plus"></i> Add Childs '.$parent->name.'</button>';
                    echo '<p><a class="text-white" href="'.$url.'"><i class="fa fa-mail-reply"></i></a> Parent Category : <span class="text-warning">'.$parent->name.'</span></p>';
                }else{
                    echo '<button class="btn btn-xs btn-warning rounded-0 category_modal mb-5" data-url="'.route('product.category.store').'" data-type="add" data-title="Add Category" data-toggle="modal" data-target="#category-modal" type="button"><i class="fa fa-plus"></i> Add Category</button>';
                }
            @endphp
            <div class="ibox-content">
                <!-- Table Responsive -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                            <th width="3%">No</th>
                            <th>Name</th>
                            <th width="25%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data->count()>0)
                            @foreach ($data as $key => $h)
                                <tr>
                                    <td>{{++$key}}</td>
                                    <td><a class="text-warning" href="{{($h->childs()->count() > 0)? route('product.category.show',$h->id) : '#'}}">{{$h->name}}</a></td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-warning rounded-0 category_modal" data-url="{{ route('product.category.store')}}" data-type="add_child" data-title="Add Childs Category" data-parent="{{$h->id}}" data-name="{{$h->name}}" data-toggle="modal" data-target="#category-modal" type="button"><i class="fa fa-plus"></i> Add Childs</button>
                                        <button class="btn btn-xs btn-info rounded-0 category_modal" data-url="{{ route('product.category.update',$h->id)}}" data-type="update" data-title="Update Category" data-parent="{{$h->parent_id}}" data-name="{{$h->name}}" data-toggle="modal" data-target="#category-modal" type="button"><i class="fa fa-edit"></i> Update</button>
                                        {{-- <a class="btn btn-xs btn-danger" href="{{ route('product.category.delete',$h->id)}}" type="button">Delete</a> --}}
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                <tr>
                                <td colspan="6" class="text-center">No data available in table</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-white" id="title-modal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form action="" method="POST" id="category-update">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group mb-0 hidden" id="parent">
                            <label class="col-form-label">Parent Name</label>
                            <input id="parent_id" name="parent_id" type="text" class="form-control hidden" placeholder="Parent ID">
                            <input id="parent_name" type="text" class="form-control" readonly placeholder="Parent Name">
                        </div>
                        <div class="form-group mb-0">
                            <label class="col-form-label">Name</label>
                            <input id="name" type="text" name="name" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="action">
                            <button id="btn_submit" type="submit" class="btn btn-warning rounded-0">Submit</button>
                            <button type="button" class="btn btn-danger rounded-0" data-dismiss="modal">Cancel</button>
                        </div>
                        <div class="hidden" id="loader">
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
    $('.category_modal').on('click',function(){
        $('#title-modal').html($(this).data('title'));
        $('#category-update').attr('action',$(this).data('url'));
        $('#parent').addClass('hidden');
        if($(this).data('type') == 'update'){
            $('#parent_name').val();
            $('#parent_id').val($(this).data('parent'));
            $('#name').val($(this).data('name'));
        }else if($(this).data('type') == 'add_child'){
            $('#parent').removeClass('hidden');
            $('#parent_id').val($(this).data('parent'));
            $('#parent_name').val($(this).data('name'));
            $('#name').val();
        }else{
            $('#parent_id').val();
            $('#parent_name').val();
            $('#name').val();
        }
    });
    $('#btn_submit').on('click',function () {
        $('#action').addClass('hidden');
        $('#loader').removeClass('hidden');
    });
</script>
@endsection
