<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Balance;
use App\Models\Convert;
use Illuminate\Console\Command;
use App\Models\HistoryTransaction;

class FeeVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fee:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fee Voucher';

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
        $type_wallet = 'Cash Wallet';
        $data = Convert::where('type','voucher')->where('id','>',85)->get();
        foreach ($data as $key => $value) {
            $fee = $value->fee;
            $date = $value->created_at;
            $cash = $value->user->balance()->where('description',$type_wallet)->first();
            $cash->balance = $cash->balance - $fee;
            $cash->save();
            $his = HistoryTransaction::where('balance_id',$cash->id)->where('description','like','%Convert Cash Wallet to Voucher%')->where('created_at',$date)->first();
            $his->amount = $value->total;
            $his->save();
            $balance_adm = Balance::where(['user_id'=> 1,'description'=> $type_wallet])->first();
            $balance_adm->balance = $balance_adm->balance + $fee;
            $balance_adm->save();
            $hisa = HistoryTransaction::where('balance_id',$balance_adm->id)->where('description','like','%Convert Cash Wallet to Voucher%')->where('created_at',$date)->first();
            $hisa->amount = $value->total;
            $hisa->save();
            echo $value->id."\n";
        }
    }
}
