<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ico extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'sold',
        'rest',
        'price',
        'min_buy',
        'status'
    ];
}
