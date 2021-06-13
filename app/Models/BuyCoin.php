<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyCoin extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
    	'price',
    	'total',
    	'status',
    	'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
