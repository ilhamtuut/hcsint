<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAction extends Model
{

    protected $fillable = [
    	'user_id',
    	'product_id',
        'type',
    	'ip_visitor',
    	'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
