<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInBuyCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buy_coins', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->double('amount',20,8)->default(0)->after('user_id');
            $table->double('price',20,8)->default(0)->after('amount');
            $table->double('total',20,8)->default(0)->after('price');
            $table->integer('status')->default(0)->after('total');
            $table->string('description')->nullable()->after('status');
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buy_coins', function (Blueprint $table) {
            //
        });
    }
}
