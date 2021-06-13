<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'username',
        'email',
        'phone_number',
        'country',
        'status',
        'session_id',
        'api_token',
        'password',
        'trx_password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'trx_password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function childs()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function balance()
    {
        return $this->hasMany(Balance::class, 'user_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function bonus_active()
    {
        return $this->hasMany(BonusActive::class, 'user_id');
    }

    public function bonus_pasif()
    {
        return $this->hasMany(BonusPasif::class, 'user_id');
    }

    public function withdraw()
    {
        return $this->hasMany(Withdraw::class, 'user_id');
    }

    public function program()
    {
        return $this->hasMany(Program::class, 'user_id');
    }

    public function convert()
    {
        return $this->hasMany(Convert::class, 'user_id');
    }

    public function tree()
    {
        return $this->hasOne(Tree::class, 'user_id');
    }

    public function is_max($bonus)
    {
        $max_profit = $this->program()->sum('max_profit');
        $active = $this->bonus_active()->sum('bonus');
        $pasif = $this->bonus_pasif()->sum('bonus');
        $total_bonus = $active + $pasif;
        $currentBonus = $total_bonus + $bonus;
        $lost = 0;
        if($currentBonus > $max_profit){
            $kurang = $max_profit - $total_bonus;
            $lost = $bonus - $kurang;
            $bonus = $kurang;
        }
        $data = array(
                    'max_profit' => ($total_bonus < $max_profit) ? true : false,
                    'bonus' => $bonus,
                    'lost' => $lost,
                );
        return $data;
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'user_id');
    }

    public function question()
    {
        return $this->hasOne(Answer::class, 'user_id');
    }
}
