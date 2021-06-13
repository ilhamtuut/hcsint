<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('balance_id')->unsigned();
            $table->unsignedBigInteger('from_id')->unsigned();
            $table->unsignedBigInteger('to_id')->unsigned();
            $table->double('amount',20,4)->default(0);
            $table->string('description')->nullable();
            $table->integer('status')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

            $table->foreign('balance_id')->references('id')->on('balances');
            $table->foreign('from_id')->references('id')->on('users');
            $table->foreign('to_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_transactions');
    }
}
