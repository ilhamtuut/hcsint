<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Package;
use App\Models\Balance;
use App\Models\Program;
use App\Models\Downline;
use App\Models\BonusPasif;
use App\Models\Portofolio;
use App\Models\BonusActive;
use App\Models\Composition;
use App\Models\LogGenerate;
use App\Models\HistoryTransaction;
use Illuminate\Console\Command;

class GenerateBonusPasif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:bonus_pasif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Bonus Share Profit Trade';

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
     * @return mixed
     */
    public function handle()
    {
        $datenow = date('Y-m-d');
        $description = 'Bonus Share Profit Trade';
        $program = Program::where('status', 0)
                    ->whereNotIn('user_id', [1,2])
                    ->whereDate('created_at','!=',$datenow)
                    ->get();
        $checkFolio = Portofolio::whereIn('type',['Regular','Networker'])
                    ->whereDate('created_at', $datenow)->first();
        if(is_null($checkFolio)){
            $percentDay = Package::where('description','Regular')->first()->roi;
            Portofolio::create([
                'type' => 'Regular',
                'percent' => $percentDay
            ]);

            $percentDay = Package::where('description','Networker')->first()->roi;
            Portofolio::create([
                'type' => 'Networker',
                'percent' => $percentDay
            ]);
        }
        if(count($program) > 0){
            LogGenerate::create([
                'activity'=>'Generate Bonus ROI Start',
                'status'=>1
            ]);
            foreach ($program as $key => $value) {
                echo ++$key." - \n";
                $id = $value->id;
                $user_id = $value->user_id;
                $amount = $value->amount;
                $percent = $value->package->roi;
                $bonus = $amount * $percent;
                $type = $value->package->description;
                $wallet_name = 'Cash Wallet';
                if($type == 'Networker'){
                    $wallet_name = 'Register Wallet';
                }
                $maxBonus = $value->user->is_max($bonus);
                if($maxBonus['max_profit']){
                    $bonus = $maxBonus['bonus'];
                    $lost = $maxBonus['lost'];
                    $check = $value->bonus()->whereDate('created_at',$datenow)->first();
                    if(is_null($check) && $bonus > 0){
                        BonusPasif::create([
                            'user_id' => $user_id,
                            'program_id' => $id,
                            'amount' => $amount,
                            'percent' => $percent,
                            'bonus' => $bonus,
                            'lost' => $lost,
                            'status' => 1,
                            'description' => $description
                        ]);

                        $cryptoAdmin = Balance::where(['user_id'=>1,'description'=> $wallet_name])->first();
                        $cryptoAdmin->balance = $cryptoAdmin->balance - $bonus;
                        $cryptoAdmin->save();

                        HistoryTransaction::create([
                            'balance_id'=>$cryptoAdmin->id,
                            'from_id'=> 1,
                            'to_id'=> $user_id,
                            'amount'=> $bonus,
                            'description'=> $description.' to '.ucfirst($value->user->username),
                            'status'=> 1,
                            'type'=> 'OUT',
                            'balance_type'=> 'bonus',
                        ]);

                        $crypto = $value->user->balance()->where('description', $wallet_name)->first();
                        $crypto->balance = $crypto->balance + $bonus;
                        $crypto->save();

                        HistoryTransaction::create([
                            'balance_id'=> $crypto->id,
                            'from_id'=> $user_id,
                            'to_id'=> $user_id,
                            'amount'=> $bonus,
                            'description'=> $description,
                            'status'=> 1,
                            'type'=> 'IN',
                            'balance_type'=> 'bonus',
                        ]);
                    }
                }else{
                    $value->status = 1;
                    $value->save();
                }
            }
            LogGenerate::create([
                'activity'=>'Generate Bonus Share Profit Trade End',
                'status'=>1
            ]);
        }else{
            LogGenerate::create([
                'activity'=>'Generate Bonus Share Profit Trade, Data Not Found',
                'status'=>1
            ]);
        }
        echo "Selesai\n";
    }
}
