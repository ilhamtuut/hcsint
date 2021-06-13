<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInConvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('converts', function (Blueprint $table) {
            $table->double('total',20,8)->default(0)->after('price');
            $table->double('fee',20,8)->default(0)->after('total');
            $table->double('additional',20,8)->default(0)->after('fee');
            $table->text('json',20,8)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('converts', function (Blueprint $table) {
            //
        });
    }
}
