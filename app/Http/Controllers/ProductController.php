<?php

namespace App\Http\Controllers;

use Auth;
use App\Helpers\Address;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAction;
use App\Models\ProductAddress;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = $request->category;
 		$search = $request->search;
 		$status = $request->status;
 		$data = Product::when($search, function ($query) use ($search){
 					$query->where('name','like','%'.$search.'%');
 				})
 				->when($status, function ($query) use ($status){
 					$query->where('status', $status);
 				})
 				->when($category, function ($query) use ($category){
 					$query->whereHas('category', function ($q) use ($category){
 						$categories = ProductCategory::find($category)->childs()->pluck('id')->toArray();
 						array_push($categories, $category);
 						$q->whereIn('categories.id',$categories);
 					});
 				})
 				->paginate(10);
        $categories = ProductCategory::whereNull('parent_id')
            ->with('childs')
            ->orderBy('name')->get();
        return view('backend.marketplace.product.index',compact('data','categories'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function myProduct(Request $request)
    {
        $category = $request->category;
 		$search = $request->search;
 		$status = $request->status;
 		$data = Product::where('seller_id',Auth::id())->when($search, function ($query) use ($search){
 					$query->where('name','like','%'.$search.'%');
 				})
 				->when($status, function ($query) use ($status){
 					$query->where('is_show', $status);
 				})
 				->when($category, function ($query) use ($category){
 					$query->whereHas('category', function ($q) use ($category){
 						$categories = ProductCategory::find($category)->childs()->pluck('id')->toArray();
 						array_push($categories, $category);
 						$q->whereIn('categories.id',$categories);
 					});
 				})
 				->paginate(10);
        $categories = ProductCategory::whereNull('parent_id')
            ->with('childs')
            ->orderBy('name')->get();
        return view('backend.marketplace.product.myProduct',compact('data','categories'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Auth::user()->hasRole('member') && !Auth::user()->program()->first()){
            $request->session()->flash('failed', 'Please buy the package first to be able to add products to the marketplace.');
            return redirect()->route('program.index');
        }
        $categories = ProductCategory::whereNull('parent_id')
                    ->with('childs')
                    ->orderBy('name')->get();
        return view('backend.marketplace.product.create',compact('categories'));
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
            'name' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|integer',
            'condition' => 'required|string',
            'description' => 'required',
            'province' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'address' => 'required',
            'image' => 'required',
            'image.*' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::create([
    		'seller_id' => Auth::id(),
	    	'category_id' => $request->category,
	        'name' => $request->name,
	        'price' => $request->price,
	        'description' => $request->description,
	        'type' => 'Free',
	        'condition' => $request->condition,
	        'is_show' => 1,
	        'status' => 1
    	]);

        ProductAddress::create([
            'product_id' => $product->id,
            'name' => $request->name,
            'province' => $request->province,
            'district' => $request->district,
            'sub_district' => $request->sub_district,
            'address' => $request->address,
            'status' => 1
        ]);

    	if($request->hasFile('image')){
	    	foreach ($request->file('image') as $image) {
            	$nameimage = uniqid().'.'.$image->getClientOriginalExtension();
            	$image->move('product/',$nameimage);
	    		ProductImage::create([
	            	'product_id' => $product->id,
	    			'name' => $nameimage
	            ]);
	    	}
    	}
        $request->session()->flash('success', 'Successfully, add product');

    	return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $data = Product::where('id',$id)->withCount('like','dislike','follower','views')->first();
        if(is_null($data)){
            abort(404);
        }
        $ip_visitor = $_SERVER['REMOTE_ADDR'];
        $check = ProductAction::where(['product_id'=> $id, 'ip_visitor'=> $ip_visitor])->exists();
        if(!$check){
            ProductAction::create([
                'product_id'=> $id,
                'user_id'=> Auth::id(),
                'type'=>'views',
                'ip_visitor'=> $ip_visitor
            ]);
        }
        return view('backend.marketplace.product.show',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $categories = ProductCategory::whereNull('parent_id')
                    ->with('childs')
                    ->orderBy('name')->get();
        $data = Product::find($id);
        if(is_null($data)){
            abort(404);
        }
        return view('backend.marketplace.product.edit',compact('categories','data'));
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
            'name' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|integer',
            'condition' => 'required|string',
            'description' => 'required',
            'province' => 'required',
            'district' => 'required',
            'sub_district' => 'required',
            'address' => 'required',
            'image.*' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::find($id);
        $product->update([
	    	'category_id' => $request->category,
            'address_id' => $request->address,
	        'name' => $request->name,
	        'price' => $request->price,
	        'description' => $request->description
    	]);

        $address = $product->address;
        $address->province = $request->province;
        $address->district = $request->district;
        $address->sub_district = $request->sub_district;
        $address->address = $request->address;
        $address->save();

		$fileImg = ProductImage::where('product_id',$id)->whereNotIn('id',$request->file)->get();
    	foreach ($fileImg as $val) {
    		if($val->name && !empty($val->name) && !is_null($val->name)){
                $img = public_path('product/'.$val->name);
                if(file_exists($img)){
                    unlink($img);
                }
            }
        	$val->delete();
    	}

    	if($request->hasFile('image')){
	    	foreach ($request->file('image') as $image) {
            	$nameimage = uniqid().'.'.$image->getClientOriginalExtension();
            	$image->move('product/',$nameimage);
	    		ProductImage::create([
	            	'product_id' => $id,
	    			'name' => $nameimage
	            ]);
	    	}
    	}
        $request->session()->flash('success', 'Successfully, updated product');
    	return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //
    }

    public function enableProduct(Request $request,$id)
    {
    	$data = Product::find($id);
    	$status = 1;
    	if($data->status == 1){
	    	$status = 2;
    	}
    	$data->status = $status;
    	$data->save();
    	return redirect()->back();
    }

    public function publishProduct(Request $request,$id)
    {
        $data = Product::find($id);
    	$status = 1;
    	if($data->is_show == 1){
	    	$status = 2;
    	}
    	$data->is_show = $status;
    	$data->save();
    	return redirect()->back();
    }

    public function likeOrDislike(Request $request,$type,$product_id)
 	{
        $ip_visitor = $_SERVER['REMOTE_ADDR'];
        $product = Product::where('id',$product_id)->withCount('like','dislike','follower','views')->first();
        if($product){
 			$check = ProductAction::where([
                        'product_id'=>$product_id,
 						'user_id'=>Auth::id(),
                        'type'=>$type
 					])->first();
 			if($check){
 				$status = 1;
				$description = $type;
 				if($check->status == 1){
	 				$status = 2;
 					$description = 'canceled '.$type;
 				}
 				if($check->type != $type){
 					$check->type = $type;
	 				$status = 1;
 				}
 				$check->status = $status;
 				$check->ip_visitor = $ip_visitor;
 				$check->save();
 			}else{
				$description = $type;
                ProductAction::create([
                    'product_id'=> $product_id,
                    'user_id'=> Auth::id(),
                    'type'=>$type,
                    'ip_visitor'=> $ip_visitor
                ]);
		 	}

            if($type == 'like'){
                ProductAction::where([
                    'product_id'=>$product_id,
                    'user_id'=>Auth::id(),
                    'type'=>'dislike'
                ])->update(['status'=>2]);
            }elseif($type == 'dislike'){
                ProductAction::where([
                    'product_id'=>$product_id,
                    'user_id'=>Auth::id(),
                    'type'=>'like'
                ])->update(['status'=>2]);
            }
            $like = ProductAction::where([
                'product_id'=> $product_id,
                'type'=>'like',
                'status'=>1,
            ])->count();
            $dislike = ProductAction::where([
                'product_id'=> $product_id,
                'type'=>'dislike',
                'status'=>1,
            ])->count();
            $views = ProductAction::where([
                'product_id'=> $product_id,
                'type'=>'views',
                'status'=>1,
            ])->count();
            $action = array(
                'like_count' => $like,
                'dislike_count' => $dislike,
                'views_count' => $views
            );
	 		$data = array(
	            'success' => true,
	            'msd'=> 'Successfully '.$description.' this product',
                'data' => $action
	        );
	 	}else{
	 		$data = array(
	            'success' => false,
	            'msd'=> 'Produk not found'
	        );
	 	}
        return response()->json($data);
 	}
}
