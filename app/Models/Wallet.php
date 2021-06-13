<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'secret_key',
        'balance',
    	'currency',
    	'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function history()
    {
        return WalletTransaction::where('from_address', $this->address)
            ->orWhere('to_address', $this->address);
    }
}
