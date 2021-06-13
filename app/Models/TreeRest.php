<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreeRest extends Model
{
    protected $fillable = [
        'user_id',
        'right',
        'left',
        'position',
        'status'
    ];
}
