@extends('layouts.backend',['page'=>'product','active'=>'list_product'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_cart_alt"></i> <span>List Product</span></h4>
@endsection

@section('content')
    <div class="col-lg-12">
        @include('layouts.partials.alert')
    </div>
    <div class="col-lg-12">
        <!-- Product List Area -->
        <div class="product-list--area bg-boxshadow">
            <div class="ibox-content">
            <form action="{{route('product.index')}}" id="form-search" action="GET">
                <div class="row mb-30">
                    <div class="col-sm-4">
                    <select id="category" name="category" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;" data-width="100%" data-size="auto" title="Select Category">
                        {{-- <option value="">Select Category</option> --}}
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
                    <div class="col-sm-4">
                    <select name="status" class="selectpicker" title="Select Status" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                        {{-- <option value="">Select Status</option> --}}
                        <option value="1" @if(request()->status == 1) selected @endif>Published</option>
                        <option value="2" @if(request()->status == 2) selected @endif>Unpublished</option>
                    </select>
                    </div>
                    <div class="col-sm-4">
                    <div class="input-group">
                        <input name="search" class="form-control" type="text" placeholder="Product Name" required>
                        <span class="input-group-append">
                            <button type="button" onclick="submit();" class="btn btn-warning"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </div>
                </div>
            </form>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Ibox -->
                    <div class="ibox">
                        <!-- Ibox Content -->
                        <div class="ibox-content">
                            <!-- Footable -->
                            <table class="footable table table-stripped toggle-arrow-tiny">
                                <thead>
                                    <tr>
                                        <th width="3%">#</th>
                                        <th data-toggle="true">Product Name</th>
                                        <th data-hide="phone">Price</th>
                                        <th data-hide="phone,tablet">Seller</th>
                                        <th data-hide="phone,tablet">Category</th>
                                        {{-- <th data-hide="phone,tablet">Condition</th> --}}
                                        <th data-hide="phone">Condition</th>
                                        <th data-hide="all">Description</th>
                                        <th data-hide="phone" class="text-center">Published</th>
                                        <th data-hide="phone" class="text-center">Status</th>
                                        <th class="text-right" data-sort-ignore="true">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($data as $value)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>{{number_format($value->price)}}</td>
                                        <td><span class="text-warning">{{ucfirst($value->seller->name)}}</span></td>
                                        <td><span class="text-warning">{{($value->category->parent) ? $value->category->parent->name.' >' : ''}} {{$value->category->name}} </span></td>
                                        {{-- <td>{{$value->condition}}</td> --}}
                                        <td>{{$value->condition}}</td>
                                        <td><span class="text-warning">{{$value->description}}</span></td>
                                        <td class="text-center">
                                        @if($value->is_show == 1)
                                            <span class="label label-info">Yes</span>
                                        @else
                                            <span class="label label-danger">No</span>
                                        @endif
                                        </td>
                                        <td class="text-center">
                                        @if($value->status == 1)
                                            <span class="label label-info">Enabled</span>
                                        @else
                                            <span class="label label-danger">Disabled</span>
                                        @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('product.show',$value->id)}}" class="btn-white btn btn-xs">View</a>
                                                <a href="{{route('product.edit',$value->id)}}" class="btn-white btn btn-xs">Edit</a>
                                                <a href="{{route('product.publishProduct',$value->id)}}" class="btn-white btn btn-xs">@if($value->is_show == 1) Unpublished @else Published @endif</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="10" class="text-center">No data available in table</td>
                                    </tr>
                                @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                        {!! $data->appends(['status'=>request()->status,'category'=>request()->category])->render() !!}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
