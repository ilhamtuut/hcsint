<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->unsigned();
            $table->unsignedBigInteger('category_id')->unsigned();
            $table->string('name')->nullable();
            $table->double('price',20,4)->default(0);
            $table->decimal('discount',8,4)->default(0);
            $table->integer('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('condition')->nullable();
            $table->integer('is_show')->default(1);
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('product_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
