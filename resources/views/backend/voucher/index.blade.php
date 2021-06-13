@extends('layouts.backend',['page'=>'voucher','active'=>'list'])

@section('header')
  <h4 class="font-color-purple"><i class="icon_creditcard"></i> <span>Data Voucher</span></h4>
@endsection

@section('content')
<div class="col-lg-12">

    <div class="ibox-home bg-boxshadow">
        <!-- Ibox Content -->
        <div class="ibox-content">
            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="table dataTables">
                    <thead>
                        <tr>
                          <th width="3%" class="text-center">No</th>
                          <th>Kode Voucher</th>
                          <th>Name</th>
                          <th>Price</th>
                          <th>Activation Date</th>
                          <th>Expired Date</th>
                          <th>Used Date</th>
                          <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="body-block">
                        <tr>
                            <td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .form-inline{
        display: inline;
        -webkit-box-pack: unset;
        -ms-flex-pack: unset;
        justify-content: unset;
    }

    .form-inline label{
        display: inline;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-right: 0px;
    }
</style>
@endsection
@section('script')
    <script type="text/javascript">
        $('document').ready(function () {
            var data = @json(\App\Helpers\Voucher::data_voucher(''));
            console.log(data);
            $('#body-block').children().remove();
            if(data != null && data.length > 0 ){
                $.each(data, function (i,item) {
                    var status = '<span class="badge p-1 badge-danger">Not Used</span>';
                    if(item.status == 2){
                        var status = '<span class="badge p-1 badge-success">Used</span>';
                    }
                    if(item.id_paket_voucher == 222){
                        var name_voucher = 'Indomart 50.000';
                        var price = 50000;
                    }else if(item.id_paket_voucher == 296){
                        var name_voucher = 'Indomart 100.000';
                        var price = 100000;
                    }else if(item.id_paket_voucher == 297){
                        var name_voucher = 'Alfamart 50.000';
                        var price = 50000;
                    }

                    $('#body-block').append(
                        '<tr>'+
                            '<td class="text-center">'+ ++i +'</td>'+
                            '<td>'+ hideCode(item.kode) +'</td>'+
                            '<td>'+ name_voucher +'</td>'+
                            '<td>'+ addCommas(price) +'</td>'+
                            '<td>'+ item.tgl_aktifasi +'</td>'+
                            '<td>'+ item.tgl_kadaluarsa +'</td>'+
                            '<td>'+ item.tgl_pakai +'</td>'+
                            '<td class="text-center">'+ status +'</td>'+
                        '</tr>');
                });

                $('.dataTables').DataTable({
                    pageLength: 20,
                    responsive: true,
                    bLengthChange: false,
                    bInfo: false
                });
            }else{
                $('#body-block').append('<tr><td colspan="8" class="text-center">No data available in table</td></tr>');
            }
        });

        function hideCode(code) {
            var start = code.substr(0, 4);
            var end = code.substr(-4);
            return start+"****"+end;
        }
    </script>
@endsection
