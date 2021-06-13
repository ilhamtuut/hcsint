@extends('layouts.backend',['page'=>'products','active'=>'detail_product'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_cart_alt"></i> <span>Detail Product</span></h4>
@endsection

@section('content')
<div class="col-12">
    <div class="product--item-arae bg-boxshadow">
        <!-- Media -->
        <div class="media align-items-center py-3 mb-4">
            <img src="{{$data->image[0]->link_image}}" alt="" class="product-thumb d-block">
            <!-- Media Body -->
            <div class="media-body ml-4">
                <h4 class="mb-15">{{$data->name}}</h4>
                <a href="#" class="label label-warning"><i class="fa fa-eye"></i> <span id="views_count">{{$data->views_count}}</span></a>
                <a href="#" class="label label-warning" onclick="likeDislike('like',{{$data->id}})"><i class="icon_like"></i> <span id="like_count">{{$data->like_count}}</span></a>
                <a href="#" class="label label-warning" onclick="likeDislike('dislike',{{$data->id}})"><i class="icon_dislike"></i> <span id="dislike_count">{{$data->dislike_count}}</span></a>
            </div>
        </div>

        <!-- Nav Tabs -->
        <div class="nav-tabs-top">
            <ul class="nav nav-tabs">
                <!-- Nav Item -->
                <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#item-overview">Overview</a>
                </li>
                <!-- Nav Item -->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#item-images">Images</a>
                </li>
                <!-- Nav Item -->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#item-seller">Seller</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview -->
                <div class="tab-pane fade active show" id="item-overview">
                    <!-- Card Body -->
                    <div class="card-body">
                        <h6 class="large font-weight-semibold mb-4">Basic info</h6>
                        <!-- Product Item table -->
                        @php
                            $price = $data->price;
                            $discount = $data->price - ($data->price * $data->discount);
                        @endphp
                        <table class="table product-item-table">
                            <tbody>
                                <tr>
                                    <td>Price:</td>
                                    <td>
                                        <strong>
                                            @if($discount == $data->price)
                                              {{number_format($data->price)}}
                                            @else
                                              <strike>{{number_format($data->price)}}</strike> / {{number_format($discount)}}
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Category:</td>
                                    <td>{{($data->category->parent) ? $data->category->parent->name.' >' : ''}} {{$data->category->name}}</td>
                                </tr>
                                {{-- <tr>
                                    <td>Type Ads:</td>
                                    <td>{{$data->type}} @if($data->type == 'Paid') / {{$data->expired_date}} @endif</td>
                                </tr> --}}
                                <tr>
                                    <td>Condition:</td>
                                    <td>{{($data->condition)}}</td>
                                </tr>
    			                @if($data->seller_id == Auth::id() || !Auth::user()->hasRole('member'))
                                    <tr>
                                        <td>Published:</td>
                                        <td>
                                            @if($data->is_show == 1)
                                                <span class="badge badge-success">Published</span>
                                            @else
                                                <span class="badge badge-danger">Unpublished</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td>
                                            @if($data->status == 1)
                                            <span class="badge badge-success">Enabled</span>
                                            @else
                                            <span class="badge badge-danger">Disabled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>Description:</td>
                                    <td>{!! $data->description !!}</td>
                                </tr>
                                @if($data->type == 'Sell')
                                    <tr>
                                        <td>Address:</td>
                                        <td>{{$data->address->address}}, {{$data->address->sub_district}}, {{$data->address->district}}, {{$data->address->province}}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="tab-pane fade" id="item-images">
                    <div class="card-body">
                        <div class="mb-4 text-warning">
                            <span class="badge badge-dot badge-warning"></span> Primary image
                        </div>
                        <div id="product-item-images" class="row">
                        	@foreach($data->image as $value)
	                            <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-4">
	                                <a href="#" class="d-block"><img src="{{$value->link_image}}" class="img-fluid" alt=""></a>
	                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="item-seller">
                    <div class="card-body">
                        <h6 class="large font-weight-semibold mb-4">Seller</h6>
                        <!-- Product Item table -->
                        <table class="table product-item-table">
                            <tbody>
                                <tr>
                                    <td>Username:</td>
                                    <td>{{ucfirst($data->seller->username)}}</td>
                                </tr>
                                <tr>
                                    <td>Name:</td>
                                    <td>{{ucfirst($data->seller->name)}}</td>
                                </tr>
                                <tr>
                                    <td>Phone Number:</td>
                                    <td>{{$data->seller->phone_number}}</td>
                                </tr>
                                <tr>
                                    <td>Address:</td>
                                    <td>{{$data->address->address}}, {{$data->address->sub_district}}, {{$data->address->district}}, {{$data->address->province}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .nav-tabs > li > a{
        color: #e7bd29;
    }

    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{
        color: #f8f9fa;
        background-color: #e7bd29;
        border-color: #e7bd29 #e7bd29 #e7bd29;
    }

    .product-thumb{
        border: 1px solid #e7bd29;
        padding: 1px;
    }
</style>
@endsection
@section('script')
<script>
    function likeDislike(type,id) {
        $.ajax({
            type: 'GET',
            url: '{{url('product/action')}}/'+type+'/'+id,
            dataType: 'json',
            success: function(data){
                if(data.success){
                    $('#views_count').html(data.data.views_count);
                    $('#like_count').html(data.data.like_count);
                    $('#dislike_count').html(data.data.dislike_count);
                }
            }
        });
    }
</script>
@endsection
