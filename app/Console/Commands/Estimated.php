<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\Convert;
use App\Models\Withdraw;
use App\Models\MetaTrader;
use App\Models\EstimatedAsset;
use Illuminate\Console\Command;

class Estimated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estimated:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Estimated';

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
        $sales = Program::whereNotIn('user_id',[1,2])
                    ->where('registered_by', '!=', 0)
                    ->sum('amount');
        $withdraw = Withdraw::where('status',1)->sum('amount') + Convert::where('status',1)->sum('amount');
        $metatrader = MetaTrader::sum('nominal');
        EstimatedAsset::create([
            'sales' => $sales,
            'withdraw' => $withdraw,
            'metatrader' => $metatrader
        ]);
    }
}
