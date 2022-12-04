<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('available_quantity');
            $table->string('isbn');
            $table->string('language');
            $table->integer('total_pages');
            $table->float('price');
            $table->string('book_image');
            $table->longText('description')->nullable();
            $table->date('published_date');
            $table->unsignedBigInteger('publisher_id');
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
            $table->unique('isbn');
            $table->softDeletes();
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
        Schema::dropIfExists('books');
    }
}
