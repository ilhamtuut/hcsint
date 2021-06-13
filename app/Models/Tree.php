<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
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

    public function parent()
    {
        return $this->belongsTo(Tree::class, 'upline_id','user_id');
    }

    public function childs()
    {
        return $this->hasMany(Tree::class, 'upline_id','user_id');
    }
}
