<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Tree;
use App\Models\User;
use App\Models\Balance;
use App\Models\Package;
use App\Models\Program;
use App\Models\Downline;
use App\Models\TreeRest;
use App\Models\TreeUpline;
use App\Models\BonusActive;
use App\Models\Composition;
use App\Models\LogGenerate;
use App\Models\HistoryTransaction;
use Illuminate\Console\Command;

class BonusPairing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:bonus_pairing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate bonus pairing';

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
        $lastdate  = date('Y-m-d',strtotime('-1 day', strtotime($datenow)));
        $tree = Tree::select('user_id','upline_id')
            ->where('user_id', '!=', 2)
            ->whereDate('created_at','!=',$datenow)
            ->orderBy('id')->get();
        if(count($tree) > 0){
            LogGenerate::create([
                'activity'=>'Generate Bonus Pairing Start',
                'status'=>1
            ]);
            foreach ($tree as $key => $value){
                echo ++$key." - \n";
                $user_id = $value->user_id;
                $upline_id = $value->upline_id;
                $program = Program::where(['user_id'=>$user_id])->whereIn('status',[0,2])
                            ->orderBy('amount','desc')->first();
                if($program){
                    $tempat = "";
                    $sisa_right = 0;
                    $sisa_left = 0;
                    $rest = TreeRest::where('user_id',$user_id)
                        ->whereDate('created_at','!=',$datenow)
                        ->orderBy('id','desc')
                        ->first();
                    if($rest){
                        $tempat = $rest->position;
                        $sisa_right = $rest->right;
                        $sisa_left = $rest->left;
                    }
                    $posll = TreeUpline::where(['upline_id'=>$user_id,'position'=>'L'])
                            ->whereDate('created_at',$lastdate)
                            ->sum('amount');
                    $L = TreeUpline::where(['upline_id'=>$user_id,'position'=>'L'])
                            ->whereDate('created_at',$lastdate)
                            ->first();
                    if($L){
                        $posisill = $L->position;
                    }else{
                        $posisill = "";
                    }
                    $posrr = TreeUpline::where(['upline_id'=>$user_id,'position'=>'R'])
                            ->whereDate('created_at',$lastdate)
                            ->sum('amount');
                    $R = TreeUpline::where(['upline_id'=>$user_id,'position'=>'R'])
                            ->whereDate('created_at',$lastdate)
                            ->first();

                    if($R){
                        $posisirr = $R->position;
                    }else{
                        $posisirr ="";
                    }

                    $posll = $sisa_left + $posll;
                    $posrr = $sisa_right + $posrr;

                    if ($posisill==""){
                        $posisisisa=$posisirr;
                        $nilai =  $posrr;
                    }else{
                        $posisisisa=$posisill;
                        $nilai =  $posll;
                    }

                    $xsatu=0;
                    $bagixr=0;
                    $bagixl=0;
                    if ($posrr>0){
                        if ($posll>0){
                            $xsatu=1;
                        }
                    }
                    if ($xsatu>0){
                        $bagix=$posll-$posrr;
                        $bagixl=$bagix;
                        $bagixr=0;
                        $bagi=$posrr;
                        $posisi=$posisirr;
                        $posisinya=$posisill;
                        if ($posisill==""){
                            $posisinya=$tempat;
                        }

                        if ($bagix<0){
                            $bagix=$posrr-$posll;
                            $bagixr=$bagix;
                            $bagixl=0;
                            $bagi=$posll;
                            $posisi=$posisill;
                            $posisinya=$posisirr;
                            if ($posisirr==""){
                                $posisinya=$tempat;
                            }
                        }

                        $check = BonusActive::where(['user_id'=>$user_id,'description'=>'Bonus Pairing'])
                                ->whereDate('created_at',$datenow)
                                ->first();
                        if (is_null($check)) {
                            $queryy = TreeRest::where('user_id',$user_id)->whereDate('created_at',$datenow)->first();
                            if(is_null($queryy)){
                                if ($posisinya){
                                    TreeRest::create([
                                        'user_id' => $user_id,
                                        'right' => $bagixr,
                                        'left' => $bagixl,
                                        'position' => $posisinya,
                                        'status' => 0
                                    ]);
                                }
                            }

                            $kali = $program->package->pairing;
                            $bonus = $bagi * $kali;
                            $maxBonus = $value->user->is_max($bonus);
                            if($maxBonus['max_profit']){
                                $bonus = $maxBonus['bonus'];
                                $lost = $maxBonus['lost'];
                                if($bonus > 0){
                                    BonusActive::create([
                                        'user_id' => $user_id,
                                        'from_id' => $user_id,
                                        'amount' => $bagi,
                                        'percent' => $kali,
                                        'bonus' => $bonus,
                                        'lost' => $lost,
                                        'status' => 1,
                                        'description' => 'Bonus Pairing'
                                    ]);

                                    $wallet_admin = Balance::where(['user_id'=>1,'description'=>'Register Wallet'])->first();
                                    $wallet_admin->balance = $wallet_admin->balance - $bonus;
                                    $wallet_admin->save();

                                    HistoryTransaction::create([
                                        'balance_id' => $wallet_admin->id,
                                        'from_id' => 1,
                                        'to_id' => $user_id,
                                        'amount' => $bonus,
                                        'description'=> 'Bonus Pairing to '.ucfirst($value->user->username),
                                        'status' => 1,
                                        'type' => 'OUT',
                                        'balance_type' => 'bonus'
                                    ]);

                                    $crypto = $value->user->balance()->where('description','Register Wallet')->first();
                                    $crypto->balance = $crypto->balance + $bonus;
                                    $crypto->save();

                                    HistoryTransaction::create([
                                        'balance_id'=> $crypto->id,
                                        'from_id'=> $user_id,
                                        'to_id'=> $user_id,
                                        'amount'=> $bonus,
                                        'description'=> 'Bonus Pairing',
                                        'status'=> 1,
                                        'type'=> 'IN',
                                        'balance_type' => 'bonus'
                                    ]);

                                }
                            }

                            if($rest){
                                $rest->status = 1;
                                $rest->save();
                            }
                        }

                    }else{
                        $rest = TreeRest::where('user_id',$user_id)
                                ->whereDate('created_at',$datenow)
                                ->first();
                        if (is_null($rest)) {
                            if($posisisisa){
                                $data = array(
                                    'user_id' => $user_id,
                                    'right' => $posrr,
                                    'left' => $posll,
                                    'position' => $posisisisa,
                                    'status' => 0
                                );
                                TreeRest::create($data);
                            }
                        }
                    }
                }
            }
            LogGenerate::create([
                'activity'=>'Generate Bonus Pairing End',
                'status'=>1
            ]);
        }else{
            LogGenerate::create([
                'activity'=>'Generate Bonus Pairing Data Not Found',
                'status'=>1
            ]);
        }
        echo "Selesai\n";
    }
}
