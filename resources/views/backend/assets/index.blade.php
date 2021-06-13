@extends('layouts.backend',['page'=>'estimated_asset','active'=>'asset'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_folder-open_alt"></i> <span>Estimated Asset</span></h4>
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
                <div class="text-center" id="load">
                    <i class="fa fa-spinner fa-spin" style="font-size: 24px;"></i>
                    <br>Loading ...
                </div>
                <div id="chart"></div>
                <div class="text-center mt-2" id="info_cart">
                    <ul class="list-inline">
                      <li class="text-muted"> <i class="fa fa-circle" style="color: #00a65a"></i> Total Asset (USDT)</li>
                      <li class="text-muted"> <i class="fa fa-circle" style="color: #e7bd29"></i> Total Sales (USD)</li>
                      <li class="text-muted"> <i class="fa fa-circle" style="color: #dc3545"></i> Total Withdraw (USD)</li>
                    </ul>
                  </div>
            </div>
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
                labels: ['Total Asset (USD)','Total Sales (USD)','Total Withdraw (USD)'],
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
    </script>
@endsection
