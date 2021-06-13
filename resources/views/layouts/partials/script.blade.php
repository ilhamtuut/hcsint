<!-- jQuery 2.2.4 -->
<script src="{{asset('dist/assets/js/jquery/jquery.2.2.4.min.js')}}"></script>
<!-- Bootsrap js -->
<script src="{{asset('dist/assets/js/bootstrap/popper.min.js')}}"></script>
<script src="{{asset('dist/assets/js/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/menu-active.js')}}"></script>

<script src="{{asset('dist/js/plugins-js/select-tags-js/bootstrap-select.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/select-tags-js/bootstrap-select-2.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/sweet-alert-js/sweet-alert.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/order-list-js/datapicker.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/product-list-js/footable.js')}}"></script>

<script src="{{asset('dist/js/plugins-js/morrris-graph-js/morris-raphael.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/morrris-graph-js/morris.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/moment/moment.js')}}" type="text/javascript"></script>
<script src="{{asset('dist/js/plugins-js/data-table-js/data-table.bootstrap.min.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/data-table-js/data-table.min.js')}}"></script>
<script src="{{asset('dist/js/plugins-js/data-table-js/data-table-active.js')}}"></script>
<!-- Active js -->
<script src="{{asset('dist/js/plugins-js/plugins-js/plugins.js')}}"></script>
<script src="{{asset('dist/js/active.js')}}"></script>
<script src="{{asset('dist/tree/release/go.js')}}"></script>
<script src="{{asset('dist/tree/extensions/DataInspector.js')}}"></script>
{{-- <script src="{{asset('dist/js/retina.js')}}"></script> --}}
@if (Auth::check())
    <script>
    var timeout = ({{config('session.lifetime')}} * 60000) -10;
    setTimeout(function(){
        window.location.reload(1);
    },  timeout);
    </script>
@endif
<script type="text/javascript">
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    $('.singledate').datepicker({
        format: 'dd/mm/yyyy',
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true
    });

    function copyReferal() {
        var copyText = document.getElementById("referal");
        copyText.select();
        document.execCommand("Copy");
    }

    function copyAddresWallet() {
        var addresWallet = document.getElementById("addresWallet");
        addresWallet.select();
        document.execCommand("Copy");
    }

    function copy(code) {
        var inp = document.createElement('input');
        document.body.appendChild(inp)
        inp.value = code;
        inp.select();
        document.execCommand('copy',false);
        inp.remove();
    }

    function pasteText() {
        let pasteArea = document.getElementById('text-value');
        pasteArea.value = '';

        navigator.clipboard.readText()
        .then((text)=>{
            pasteArea.value = text;
        });
    }

    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        alert('Text is copied');
    }

</script>
