@extends('layouts.backend',['page'=>'marketplace','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon-basket"></i> <span>Market Place</span></h4>
@endsection

@section('content')
    <div class="col-lg-12">
        <form action="{{route('marketplace.index')}}" id="form-search" action="GET">
            <div class="row mb-30">
                <div class="col-sm-6 mb-5">
                    <input name="from" class="form-control" type="text" placeholder="From Price" required>
                </div>
                <div class="col-sm-6 mb-5">
                    <input name="to" class="form-control" type="text" placeholder="To Price" required>
                </div>
                <div class="col-sm-4 mb-5">
                    <select id="sortNew" name="sortNew" class="selectpicker" title="Sort" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        <option value="asc">Oldest</option>
                        <option value="desc">Newest</option>
                    </select>
                </div>
                <div class="col-sm-4 mb-5">
                    <select id="sortPrice" name="sortPrice" class="selectpicker" title="Sort Price" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        <option value="asc">Lowest</option>
                        <option value="desc">Highest</option>
                    </select>
                </div>
                <div class="col-sm-4 mb-5">
                    <select id="condition" name="condition" class="selectpicker" title="Select Condition" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        <option value="New">New</option>
                        <option value="Second">Second</option>
                    </select>
                </div>
                <div class="col-sm-4 mb-5">
                    <select id="category" name="category" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;" data-width="100%" data-size="auto" title="Select Category">
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
                <div class="col-sm-4 mb-5">
                    <select id="location" name="location" class="selectpicker" title="Select Location" data-style="btn-select-tag" style="width: 100%;height: 36px;"></select>
                </div>
                <div class="col-sm-4 mb-5">
                <div class="input-group">
                    <input name="search" class="form-control" type="text" placeholder="Search Product" required>
                    <span class="input-group-append">
                        <button type="button" onclick="submit();" class="btn btn-warning"><i class="fa fa-search"></i></button>
                    </span>
                </div>
                </div>
            </div>
        </form>
    </div>
    @forelse ($products as $item)
        <div class="col-12 col-md-6 col-lg-4">
            <!-- Product Content -->
            <div class="shop-product-content mb-30">
                <!-- Img -->
                <div class="shop--img">
                    <img src="{{$item->image[0]->link_image}}" alt="img">
                </div>
                <!-- Product Text -->
                <div class="shop-product--text d-flex">
                    <div class="shop--item-desc">
                        <h6>Items</h6>
                        <span>{{$item->name}}</span>
                    </div>
                    <div class="shop--item-Rate">
                        <h6>Price</h6>
                        <span>{{$item->price}}</span>
                    </div>
                </div>
                <!-- Cart Btn -->
                <div class="add-to-cart text-center border-top">
                    <a class="btn m-2 btn-round btn-warning shop" href="{{route('product.show',$item->id)}}">View more</a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center mt-30">
            <h1 class="text-muted"><i class="icon-basket fa-4x"></i> <br>Opps, Product not found</h1>
        </div>
    @endforelse
    <div class="col-lg-12 text-center">
        <div></div>
        {!! $products->appends(['condition'=>request()->condition,'from'=>request()->from,'to'=>request()->to,'category'=>request()->category,'search'=>request()->search,'location'=>request()->location])->render() !!}
    </div>
    <style>
        .shop-product-content{
            background-color: #222;
            box-shadow: 0 0 10px 3px #ffeb3b6e;
        }
    </style>
@endsection
@section('script')
<script>
    load_data();
    function load_data() {
        $('#location').empty();
        var province = [];
        $.ajax({
        type: 'GET',
        url: '{{url('address/province')}}',
        dataType: 'json',
        success: function(data){
            $.each(data, function(i, item) {
                var selected = "";
                province[i] = "<option value='" + item.province + "' data-id='" + item.province_id + "'"+ selected +">" + item.province + "</option>";
            });
            $('#location').append(province);
            $('#location').selectpicker('refresh');
        }
        });
    }
</script>
@endsection
