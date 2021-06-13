@extends('layouts.backend',['page'=>'metatrader','active'=>'index'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_datareport"></i> <span>Meta Trader</span></h4>
@endsection

@section('content')
    <div class="col-lg-12 mb-30">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <!-- Ibox Content -->
            <div class="ibox-content">
                <div class="ibox-title mb-20">
                    <div class="row">
                        <div class="col-md-8"><h5 class="text-warning"> Chart Estimated Asset</h5></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="date" name="date" class="form-control singledate" type="text" placeholder="Date">
                                    <span class="input-group-append">
                                        <button type="button" id="btn_search" class="btn btn-warning"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card--body">
                    <h6 class="text-center text-success" style="text-transform: uppercase;">Tabung asset ini hanya menampilkan sebagian dari asset yang ada.</h6>
                    <div class="text-center" id="load">
                        <i class="fa fa-spinner fa-spin" style="font-size: 24px;"></i>
                        <br>Loading ...
                    </div>
                    <div id="chart"></div>
                    <div class="text-center mt-2" id="info_cart">
                        <ul class="list-inline">
                        <li class="text-muted"> <i class="fa fa-circle" style="color: #00a65a"></i> Total Asset (USD)</li>
                        <li class="text-muted"> <i class="fa fa-circle" style="color: #e7bd29"></i> Total Sales (USD)</li>
                        <li class="text-muted"> <i class="fa fa-circle" style="color: #dc3545"></i> Total Withdrawal (USD)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mb-30">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <div id="msg" class="alert alert-info hidden" role="alert">
            Password has been copied
            </div>
            <div class="ibox-content">
                <div class="m-b-1">
                    <div class="form-group mb-3">
                        <label>Meta Trader 4 {{ucfirst(request()->type)}}</label>
                        <select id="choose" class="selectpicker" data-style="btn-select-tag" style="width: 100%;height: 36px;">
                            <option value="">Choose Meta Trader 4</option>
                            @foreach($data as $key => $value)
                            <option value="{{$value->id}}" @if(request()->accountID == $value->accountID) selected @endif data-url="{{route('metatrader.index')}}?accountID={{$value->accountID}}">Meta Trader-{{++$key}} ({{$value->server}})</option>
                            @endforeach
                        </select>
                    </div>
                    @if($account)
                        <p>
                            Password Account :
                            <span id="pas_hide" class="text-warning">******** (NB : {{$account->type}})</span>
                            <span id="btn_show" class="badge badge-info" style="cursor: pointer;"><i class="fa fa-eye"></i> Show</span>
                            <span id="pas_show" class="badge badge-success hidden">{{$account->password}} <i class="fa fa-copy font-weight-bold" style="margin-left: 5px; cursor: pointer;" onclick="copyToClipboard('{{$account->password}}')"></i></span>
                            <span id="btn_hide" class="badge badge-danger hidden" style="cursor: pointer;"><i class="fa fa-eye-slash"></i> Hide</span>
                        </p>
                        <iframe src="https://trade.mql5.com/trade?servers={{$account->server}}&amp;startup_version=4&amp;startup_mode=login&amp;lang=en&amp;save_password=on&amp;login={{$account->accountID}}&amp;trade_server={{$account->server}}&amp;mobile=true" allowfullscreen="allowfullscreen" style="width: 100%; height: 600px; border: none;"></iframe>
                    @else
                        <div class="text-center mt-30">
                            Account Meta Trader is empty
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mb-30">
        <!-- Ibox -->
        <div class="ibox-home bg-boxshadow">
            <div class="ibox-title">
                <h5 class="text-warning">Asset Crypto</h5>
            </div>
            <hr>
            <div class="ibox-content">
                @forelse ($video as $item)
                    <p class="text-warning mb-0" style="font-size: 16px; text-transform: uppercase;">{{$item->title}}</p>
                    <p>{{$item->description}}</p>
                    @if($item->type == 'image')
                        <image src="{{$item->link_video}}" style="width: 50%"></image>
                    @else
                        <video src="{{$item->link_video}}" controls style="width: 50%"></video>
                    @endif
                @empty
                    <p>No data available in layers</p>
                @endforelse
          </div>
      </div>
    </div>
    <style>
        ul.list-inline li{
            display: inline-block;
            padding: 0 8px;
            font-size: 14px;
        }
    </style>
@endsection

@section('script')
    <script type="text/javascript">
        $('document').ready(function () {
            setTimeout(loadPrice(''), 1000);
        });

        $('#btn_search').on('click', function () {
            var value = $('#date').val();
            console.log('a')
            loadPrice(value);
        });

        function loadPrice(value) {
            $('#load').removeClass('hidden');
            $('#info_cart').addClass('hidden');
            $('#chart').empty();
            $.ajax({
                url: "{{route('asset.getDataChart')}}?date="+value,
                type: "GET",
                contentType: "application/json",
                success: function (data) {
                $('#load').addClass('hidden');
                if(data.length > 0){
                    $('#info_cart').removeClass('hidden');
                    loadChart(data);
                }else{
                    $('#chart').append('<div class="alert alert-info text-center" role="alert">No data charts</div>');
                }
                },
                cache: false
            });
        }

        function loadChart(day_data) {
            Morris.Line({
                element: 'chart',
                data: day_data,
                xkey: 'date',
                ykeys: ['metatrader','sales','withdraw'],
                labels: ['Total Asset (USD)','Total Sales (USD)','Total Withdrawal (USD)'],
                fillOpacity: 0,
                pointStrokeColors: ['#00a65a', '#e7bd29', '#dc3545'],
                behaveLikeLine: true,
                gridLineColor: '#f1f1f1',
                lineWidth: 1,
                hideHover: 'auto',
                lineColors: ['#00a65a', '#e7bd29', '#dc3545'],
                resize: true,
                dateFormat: function (x) { return moment(x).format('DD-MM-YYYY h:mm A'); },
                xLabelFormat: function (x) { return moment(x).format('h:mm A'); },
                yLabelFormat: function (y) { return parseFloat(y).toFixed(2).toString() + ' '; }
            });
        }
        function copyToClipboard(text) {
            $('#msg').removeClass('hidden');
            var input = document.createElement("input");
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand("Copy");
            input.remove();

            setTimeout(function(){
            $('#msg').addClass('hidden');
            }, 3000);
        }

        $('#btn_show').on('click', function () {
        $(this).addClass('hidden');
        $('#pas_hide').addClass('hidden');
        $('#pas_show').removeClass('hidden');
        $('#btn_hide').removeClass('hidden');
        });

        $('#btn_hide').on('click', function () {
        $(this).addClass('hidden');
        $('#pas_show').addClass('hidden');
        $('#pas_hide').removeClass('hidden');
        $('#btn_show').removeClass('hidden');
        });

        $('#choose').change(function() {
        var value = $(this).val();
        if(value != ''){
            var url = $(this).find(':selected').data('url');
            window.location.href = url;
        }
        });
    </script>
@endsection
