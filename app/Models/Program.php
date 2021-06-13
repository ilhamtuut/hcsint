<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
    	'hcs',
    	'register',
    	'cash',
    	'max_profit',
    	'status',
    	'registered_by',
    	'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function bonus()
    {
        return $this->hasMany(BonusPasif::class, 'program_id');
    }

    public function move()
    {
        return $this->hasOne(MoveProgram::class, 'program_id');
    }
}
