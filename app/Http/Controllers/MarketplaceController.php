<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAction;
use App\Models\ProductImages;
use App\Models\ProductAddress;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $category = $request->category;
        $search = $request->search;
 		$from = $request->from;
 		$to = $request->to;
        $location = $request->location;
 		$condition = $request->condition;
        $sortPrice = $request->sortPrice;
 		$sortNew = $request->sortNew;
 		$products = Product::with('seller:id,username,name,phone_number,email','category','category.parent','image','address','follower')
 				->withCount('like','dislike','follower','views')
 				->when($search, function ($query) use ($search){
 					$query->where('name','like','%'.$search.'%');
 				})
                ->when($condition, function ($query) use ($condition){
                    $query->where('condition', $condition);
                })
 				->when($category, function ($query) use ($category){
 					$query->whereHas('category', function ($q) use ($category){
 						$categories = ProductCategory::find($category)->childs()->pluck('id')->toArray();
 						array_push($categories, $category);
 						$q->whereIn('category_id',$categories);
 					});
 				})
 				->when($location, function ($query) use ($location){
 					$query->whereHas('address', function ($q) use ($location){
 						$q->where('product_addresses.province',$location)
 							->orWhere('product_addresses.district',$location)
 							->orWhere('product_addresses.sub_district',$location);
 					});
 				})
 				->when([$from, $to], function ($query) use ($from, $to){
                    if($from > 0 && $to > 0){
		           		$query->whereBetween('price', array($from, $to));
		           	}
 				})
 				->where('is_show',1)
 				->where('status',1)
                ->orderBy('views_count', 'desc')
                ->when($sortPrice, function ($query) use ($sortPrice){
                    $query->orderBy('price',$sortPrice);
                })
                ->when($sortNew, function ($query) use ($sortNew){
                    $query->orderBy('id',$sortNew);
                })
 				->inRandomOrder()
 				->paginate(20);
        $categories = ProductCategory::whereNull('parent_id')
                ->with('childs')
                ->orderBy('name')->get();
        return view('backend.marketplace.index',compact('products','categories'))->with('i', (request()->input('page', 1) - 1) * 20);
    }
}
