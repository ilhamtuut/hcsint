<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'block',
        'hash',
        'from_address',
        'to_address',
        'amount',
    	'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->block = self::lastBlock();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public static function lastBlock()
    {
        $block = 1;
        $latestBlock = self::latest()->first();
        if($latestBlock){
            $block = $latestBlock->block;
            $count = self::where('block', $block)->count();
            if($count == 50){
                $block = $block + 1;
            }
        }
        return $block;
    }
}
