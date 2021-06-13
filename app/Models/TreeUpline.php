<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreeUpline extends Model
{
    protected $fillable = [
        'user_id',
        'upline_id',
        'amount',
        'position',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function upline()
    {
        return $this->belongsTo(User::class, 'upline_id');
    }
}
