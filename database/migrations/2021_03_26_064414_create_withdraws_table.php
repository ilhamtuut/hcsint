<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->double('amount',20,8)->default(0);
            $table->double('fee',20,8)->default(0);
            $table->double('price',20,8)->default(0);
            $table->double('total',20,8)->default(0);
            $table->double('receive',20,8)->default(0);
            $table->integer('status')->default(0);
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->text('json_data')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('withdraws');
    }
}
