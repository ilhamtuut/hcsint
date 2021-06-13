<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimatedAsset extends Model
{
    protected $fillable = [
        'sales',
        'withdraw',
        'metatrader'
    ];
}
