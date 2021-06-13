@extends('layouts.backend',['page'=>'withdraw','active'=>'history'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_currency"></i> <span>Sell CW History</span></h4>
@endsection

@section('content')
<div class="col-12">
    <!-- Ibox -->
    <div class="ibox-home bg-boxshadow">
        <div class="ibox-title mb-20">
            <form action="{{ route('withdraw.history') }}" method="get" id="form-search">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        {{-- <select id="type" name="type" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                            <option value="">Choose type</option>
                            <option @if(request()->type == 'bank') selected @endif value="bank">Bank</option>
                            <option @if(request()->type == 'usdt') selected @endif value="usdt">USDT</option>
                        </select> --}}
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <input name="date" class="form-control singledate" type="text" placeholder="Date">
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
          </form>
        </div>

        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                          <th width="3%">#</th>
                          <th>Date</th>
                          <th>Note</th>
                          <th class="text-center">Status</th>
                          <th class="text-right">Amount</th>
                          <th class="text-right">Exchange</th>
                          <th class="text-right">Total</th>
                          <th class="text-right">Fee</th>
                          <th class="text-right">Receive</th>
                        </tr>
                    </thead>
                    <tbody>
                      	@forelse ($data as $value)
                          @php
                            $json = json_decode($value->json_data);
                          @endphp
                            <tr>
                              <td>{{++$i}}</td>
                              <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                              <td>
                                <small>
                                    Type : {{strtoupper($value->type)}} <br>
                                    @if($value->type == 'bank')
                                        Bank : {{$json->bank_name}}<br>
                                        Account Name : {{ucfirst($json->account_name)}}<br>
                                        Account Number : {{$json->account_number}}<br>
                                        @isset($json->txid)
                                            Txid : {{ucfirst($json->txid)}}
                                        @endisset
                                    @else
                                        USDT Address : {{$json->usdt_address}}<br>
                                        @isset($json->txid)
                                            Txid : {{ucfirst($json->txid)}}
                                        @endisset
                                    @endif
                                </small>
                              </td>
                              <td class="text-center">
                                @if($value->status == 0)
                                  <span class="badge p-1 badge-warning">Pending</span>
                                @elseif($value->status == 1)
                                  <span class="badge p-1 badge-success">Success</span>
                                @elseif($value->status == 2)
                                  <span class="badge p-1 badge-danger">Canceled</span>
                                @endif
                              </td>
                              <td class="text-right">{{number_format($value->amount,2)}}</td>
                              <td class="text-right">{{number_format($value->price,2)}}</td>
                              <td class="text-right">{{number_format($value->total,2)}}</td>
                              <td class="text-right">{{number_format($value->fee,2)}}</td>
                              <td class="text-right">{{number_format($value->receive,2)}}</td>
                            </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="text-center">No data available in table</td>
                          </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="6">Total</td>
                        <td class="text-right">{{number_format($total,2)}}</td>
                        <td></td>
                        <td class="text-right">{{number_format($receive,2)}}</td>
                      </tr>
                    </tfoot>
                </table>
            </div>
            {{$data->appends(['type'=>request()->type])->render()}}
        </div>
    </div>
</div>
@endsection
