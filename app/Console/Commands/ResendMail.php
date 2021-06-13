<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\InfoRegister;
use Illuminate\Support\Facades\Hash;

class ResendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resend:mail {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend Mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('username',$this->argument('username'))->first();
        if($user){
            $pass = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1,8))), 1, 8);
            $pin = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', mt_rand(1,8))), 1, 8);
            $user->password = Hash::make($pass);
            $user->trx_password = Hash::make($pin);
            $user->save();
            $data = array('password'=>$pass,'pin_authenticator'=>$pin);
            $user->notify(new InfoRegister($data));
            echo "Sent\n";
        }else{
            echo "Username Not Fount\n";
        }
    }
}
