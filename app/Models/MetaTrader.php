<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaTrader extends Model
{
    protected $fillable = [
        'name',
        'accountID',
        'password',
        'server',
        'type',
        'nominal',
        'status',
    ];
}
