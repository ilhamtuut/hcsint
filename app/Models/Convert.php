<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convert extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
    	'price',
    	'total',
    	'fee',
        'additional',
    	'receive',
    	'type',
    	'status',
    	'description',
    	'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
