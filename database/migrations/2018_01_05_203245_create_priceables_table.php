<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceablesTable extends Migration
{
    public function up()
    {
        Schema::create('priceables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('price_id')->unsigned()->references('id')->on('prices');
            $table->integer('priceable_id')->unsigned();
            $table->string('priceable_type');
            $table->dateTime('from');
            $table->dateTime('to')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('priceables');
    }
}
