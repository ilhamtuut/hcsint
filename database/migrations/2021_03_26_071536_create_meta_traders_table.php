<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaTradersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_traders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('accountID')->nullable();
            $table->string('password')->nullable();
            $table->string('server')->nullable();
            $table->string('type')->nullable();
            $table->double('nominal',20,8)->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_traders');
    }
}
