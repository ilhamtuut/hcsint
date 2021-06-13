<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddress extends Model
{
    protected $fillable = [
    	'product_id',
    	'name',
    	'province',
    	'district',
    	'sub_district',
        'address',
        'status'
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
