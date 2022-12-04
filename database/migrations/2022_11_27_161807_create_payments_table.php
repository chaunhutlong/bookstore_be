<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type');
            $table->tinyInteger('status');
            $table->float('total_book_price');
            $table->unsignedBigInteger('discount_id')->nullable();
            // $table->unsignedBigInteger('shipping_id')->nullable();
            $table->float('total');
            $table->date('paid_on')->nullable();
            $table->string('description')->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            // $table->foreign('shipping_id')->references('id')->on('shippings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};