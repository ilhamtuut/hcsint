<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{

    protected $fillable = [
    	'parent_id',
        'name',
    	'status'
    ];

    public function childs()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->with('childs');
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }
}
