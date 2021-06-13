@extends('layouts.backend',['page'=>'convert','active'=>'history_voucher'])

@section('header')
  <h4 class="font-color-purple"><i class="fa fa-exchange"></i> <span>History Convert Voucher</span></h4>
@endsection

@section('content')
    <div class="col-lg-12 mb-30">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <div class="ibox-title mb-20">
                <form action="{{ route('convert.history_voucher') }}" method="get" id="form-search">
                <div class="row">
                <div class="col-md-8"></div>
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
                            <th>Voucher</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Fee</th>
                            <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $value)
                                @php
                                    $voucher = json_decode($value->json);
                                @endphp
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{date('d F Y H:i:s', strtotime($value->created_at))}}</td>
                                    <td>
                                        <small>
                                            {{explode('Convert Cash Wallet to Voucher ',$value->description)[1]}} <br>
                                            Code : <span class="text-warning">{{$voucher->kode_voucher}} <i class="fa fa-copy" onclick="copyToClipboard('{{$voucher->kode_voucher}}')"></i></span><br>
                                            Expired Date : {{date('Y-m-d',strtotime($voucher->tgl_kadaluarsa))}}
                                        </small>
                                    </td>
                                    <td class="text-center"><span class="badge p-1 badge-success">Success</span></td>
                                    <td class="text-right">{{number_format($value->amount,2)}}</td>
                                    <td class="text-right">{{$value->fee}}</td>
                                    <td class="text-right">{{number_format($value->total,2)}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data available in table</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6">Total</td>
                            <td class="text-right">{{number_format($total,2)}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                {{$data->render()}}
            </div>
        </div>
    </div>
@endsection
