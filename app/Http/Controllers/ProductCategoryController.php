<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductCategory::whereNull('parent_id')->orderBy('name')->get();
        $type = 'parent';
        return view('backend.marketplace.product.category', compact('data','type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "parent_id" => "nullable",
            "name" => "required",
        ]);
        ProductCategory::create($request->all());
        $request->session()->flash('success', 'Successfully, Add Data Category');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ProductCategory::where('parent_id',$id)->orderBy('name')->get();
        $type = 'childs';
        return view('backend.marketplace.product.category', compact('data','type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            "parent_id" => "nullable",
            "name" => "required"
        ]);

        $update = ProductCategory::find($id)->update($request->all());
        $request->session()->flash('success', 'Successfully, Update Data Category');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = ProductCategory::find($id);
        $data->delete();
        $request->session()->flash('success', 'Successfully, Delete Data Category');
        return redirect()->back();
    }
}
