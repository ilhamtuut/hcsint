<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
    	'account_name',
    	'account_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
