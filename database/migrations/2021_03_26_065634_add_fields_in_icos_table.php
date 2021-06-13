<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInIcosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('icos', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->double('amount',20,8)->default(0)->after('name');
            $table->double('sold',20,8)->default(0)->after('amount');
            $table->double('rest',20,8)->default(0)->after('sold');
            $table->double('price',8,4)->default(0)->after('rest');
            $table->integer('min_buy')->default(0)->after('price');
            $table->integer('status')->default(0)->after('min_buy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icos', function (Blueprint $table) {
            //
        });
    }
}
