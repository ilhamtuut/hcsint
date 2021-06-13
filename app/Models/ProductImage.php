<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
    	'product_id',
    	'name'
    ];

    protected $appends = ['link_image'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getLinkImageAttribute(){
        $link = asset('product/'.$this->attributes['name']);
        return $link;
    }
}
