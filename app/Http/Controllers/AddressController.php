<?php

namespace App\Http\Controllers;

use App\Helpers\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function province(Request $request)
    {
        return Address::province();
    }

    public function district(Request $request,$id)
    {
        return Address::district($id);
    }

    public function subdistrict(Request $request,$id)
    {
        return Address::subdistrict($id);
    }
}
