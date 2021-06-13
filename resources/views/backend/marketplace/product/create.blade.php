@extends('layouts.backend',['page'=>'product','active'=>'create_product'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_cart_alt"></i> <span>Create Product</span></h4>
@endsection

@section('content')
    <div class="col-lg-12">
        @include('layouts.partials.alert')
    </div>
    <div class="col-lg-12">
        <div class="ibox-home bg-boxshadow">
            <!-- Ibox Content -->
            <div class="ibox-content">
                <form class="form-horizontal form-label-left" enctype="multipart/form-data" action="{{route('product.store')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="Name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" name="price" placeholder="Price" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Condition</label>
                                <select id="condition" name="condition" class="selectpicker" data-style="btn-select-tag">
                                    <option value="">Select Condition</option>
                                    <option value="New">New</option>
                                    <option value="Second">Second</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select id="category" name="category" class="selectpicker" data-style="btn-select-tag">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $value)
                                        <option value="{{$value->id}}">{!! $value->name !!}</option>
                                        @if ($value->childs->count())
                                            @foreach ($value->childs as $item)
                                                <option value="{{$item->id}}">-- {!! $item->name !!}</option>
                                                @if ($item->childs->count())
                                                    @foreach ($item->childs as $val)
                                                        <option value="{{$val->id}}">---- {!! $val->name !!}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <div class="input-group control-group increment">
                            <input type="file" name="image[]" class="form-control">
                            <span class="input-group-append">
                                <button class="btn btn-warning rounded-0 add-image" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>

                        <div class="clone hidden">
                            <div class="input-group control-group" style="margin-top:10px">
                                <input type="file" name="image[]" class="form-control">
                                <span class="input-group-append">
                                    <button class="btn btn-danger rounded-0" type="button"><i class="fa fa-remove"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea type="text" name="description" placeholder="Description" class="form-control" rows="5"></textarea>
                    </div>
                    <fieldset>
                        <legend>Seller's Address</legend>
                        <div class="form-group">
                            <label>Province</label>
                            <select id="province" name="province" class="selectpicker" data-style="btn-select-tag">
                                <option value="">Select province</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <select id="district" name="district" class="selectpicker" data-style="btn-select-tag">
                                <option value="">Select district</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sub District</label>
                            <select id="sub_district" name="sub_district" class="selectpicker" data-style="btn-select-tag">
                                <option value="">Select sub district</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea type="text" name="address" placeholder="Address" class="form-control" rows="5"></textarea>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="ln_solid"></div>
                    <div class="text-right" id="action">
                    <button id="btn_submit" class="btn btn-warning rounded-0" type="submit">Submit</button>
                    </div>
                    <div class="text-center hidden" id="loader">
                    <i class="fa fa-spinner fa-spin"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        fieldset{
            min-width: 0;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #e7a423;
        }

        legend{
            display: block;
            width: auto;
            max-width: 100%;
            margin-bottom: 0px;
            padding: 5px;
            font-size: 14px;
            line-height: inherit;
            color: #e7bd29;
            white-space: normal;
        }
    </style>
@endsection
@section('script')
<script type="text/javascript">
    var set_province, set_district, set_subdistrict;
    load_province();
    function load_province() {
        $('#province').empty();
        var province = [];
        $.ajax({
        type: 'GET',
        url: '{{url('address/province')}}',
        dataType: 'json',
        success: function(data){
            console.log(data)
            $('#province').append('<option value="">Choose Province</option>');
            $.each(data, function(i, item) {
            var selected = "";
            if(set_province == item.province){
                load_district(item.province_id);
                var selected = "selected";
            }
            province[i] = "<option value='" + item.province + "' data-id='" + item.province_id + "'"+ selected +">" + item.province + "</option>";
            });
            $('#province').append(province);
            $('#province').selectpicker('refresh');
        }
        });
    }

    function load_district(id) {
        $('#district').empty();
        var district = [];
        $.ajax({
        type: 'GET',
        url: '{{url('address/district')}}/'+ id,
        dataType: 'json',
        success: function(data){
            console.log(data)
            $('#district').append('<option value="">Choose District</option>');
            $.each(data, function(i, item) {
            var selected = "";
            var city_name = item.type+' '+item.city_name;
            if(set_district == city_name){
                load_subdistrict(item.city_id);
                var selected = "selected";
            }
            district[i] = "<option value='" + item.type +" "+item.city_name + "' data-id='" + item.city_id + "'"+ selected +">" + item.type +" "+item.city_name + "</option>";
            });
            $('#district').append(district);
            $('#district').selectpicker('refresh');
        }
        });
    }

    function load_subdistrict(id) {
        $('#sub_district').empty();
        var sub_district = [];
        $.ajax({
        type: 'GET',
        url: '{{url('address/subdistrict')}}/'+ id,
        dataType: 'json',
        success: function(data){
            console.log(data)
            $('#sub_district').append('<option value="">Choose Sub District</option>');
            $.each(data, function(i, item) {
            var selected = "";
            if(set_subdistrict == item.subdistrict_name){
                var selected = "selected";
            }
            sub_district[i] = "<option value='" +item.subdistrict_name + "' data-id='" + item.subdistrict_id + "'"+ selected +">" + item.subdistrict_name + "</option>";
            });
            $('#sub_district').append(sub_district);
            $('#sub_district').selectpicker('refresh');
        }
        });
    }

    $('#province').on('change', function () {
        var id = $(this).find(':selected').data('id');
        load_district(id);
    });

    $('#district').on('change', function () {
        var id = $(this).find(':selected').data('id');
        load_subdistrict(id);
    });

    $(".add-image").click(function(){
        var html = $(".clone").html();
        $(".increment").after(html);
    });
    $("body").on("click",".btn-danger",function(){
        $(this).parents(".control-group").remove();
    });

    $('#btn_submit').on('click',function () {
        $('#action').addClass('hidden');
        $('#loader').removeClass('hidden');
    });
</script>
@endsection
