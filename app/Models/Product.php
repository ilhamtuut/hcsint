<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    	'seller_id',
    	'category_id',
        'name',
        'price',
        'discount',
        'stock',
        'description',
        'type',
        'condition',
        'is_show',
    	'status'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function address()
    {
        return $this->hasOne(ProductAddress::class, 'product_id');
    }

    public function image()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function like()
    {
        return $this->hasMany(ProductAction::class, 'product_id')
        		->where(['type'=>'like','status'=>1]);
    }

    public function dislike()
    {
        return $this->hasMany(ProductAction::class, 'product_id')
        		->where(['type'=>'dislike','status'=>1]);
    }

    public function follower()
    {
        return $this->hasMany(ProductAction::class, 'product_id')
        		->where(['type'=>'follower','status'=>1]);
    }

    public function views()
    {
        return $this->hasMany(ProductAction::class, 'product_id')
        		->where(['type'=>'views','status'=>1]);
    }
}
