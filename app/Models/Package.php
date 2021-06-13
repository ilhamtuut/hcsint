<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'roi',
        'sponsor',
        'pairing',
        'max_profit',
        'description'
    ];
}
