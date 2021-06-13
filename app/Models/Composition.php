<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    protected $fillable = [
        'name',
        'one',
    	'two',
    	'three'
    ];
}
