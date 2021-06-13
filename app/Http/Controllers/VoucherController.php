<?php

namespace App\Http\Controllers;

use App\Helpers\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        // id paket 222 -> Indomaret 50.000 -> 50000, 296 -> Indomaret 100.000 -> 100000, 297 -> Alfamart 50.000 -> 50000
        return view('backend.voucher.index');
    }
}
