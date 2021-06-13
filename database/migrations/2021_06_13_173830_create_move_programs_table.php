<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoveProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('move_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->unsigned();
            $table->unsignedBigInteger('old_package_id')->unsigned();
            $table->unsignedBigInteger('new_package_id')->unsigned();

            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('old_package_id')->references('id')->on('packages');
            $table->foreign('new_package_id')->references('id')->on('packages');
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
        Schema::dropIfExists('move_programs');
    }
}
