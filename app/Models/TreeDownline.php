<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreeDownline extends Model
{
    protected $fillable = [
        'user_id',
        'downline_id',
        'amount',
        'position',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function downline()
    {
        return $this->belongsTo(User::class, 'downline_id');
    }
}
