@extends('layouts.backend',['page'=>'portofolio','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_document_alt"></i> <span>Portfolio</span></h4>
@endsection

@section('content')
<div class="col-12">
    <div class="web-icon-area bg-boxshadow">
        <form action="{{ route('portofolio.index') }}" method="get" id="form-search">
            <div class="row">
              <div class="col-md-4">
                <select name="type" class="selectpicker" data-style="btn-select-tag" type="text">
                    <option value="">Choose Plan</option>
                    <option value="Regular" @if(request()->type == 'Regular' || request()->type == '') selected @endif>Regular</option>
                    <option value="Networker" @if(request()->type == 'Networker') selected @endif>Networker</option>
                </select>
            </div>
            <div class="col-md-4">
                <input name="from_date" class="form-control singledate" type="text" placeholder="From Date">
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input name="to_date" class="form-control singledate" type="text" placeholder="To Date">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
              </div>
            </div>
          </form>
        <fieldset>
        <legend>{{date('F Y')}} => Total : {{number_format($total*100,2)}}%</legend>
            <div class="row">
                @forelse ($data as $value)
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <div class="single_icon border mb-30" style="border:1px solid #f56317 !important;">
                            <h4>{{number_format($value->percent*100,2)}}%</h4>
                            <span>{{date('Y-m-d',strtotime($value->created_at))}}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12">
                        <p class="text-center">No Data</p>
                    </div>
                @endforelse
            </div>
        </fieldset>
        {!! $data->appends(['type'=>request()->type,'from_date'=>request()->from_date,'to_date'=>request()->to_date])->render() !!}
    </div>
</div>

<style>
    fieldset{
        min-width: 0;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
    }

    legend{
        display: block;
        width: auto;
        max-width: 100%;
        margin-bottom: 0px;
        padding: 5px;
        font-size: 14px;
        line-height: inherit;
        color: #f56317;
        white-space: normal;
    }
</style>
@endsection
@section('script')
    <script type="text/javascript">

    </script>
@endsection
