<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
    	'price',
    	'total',
    	'fee',
    	'receive',
    	'status',
    	'type',
    	'description',
    	'json_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
