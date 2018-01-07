<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priceable_id');
            $table->string('priceable_type');
            $table->integer('currency_id')->unsigned()->nullable()->references('id')->on('currencies');
            $table->integer('amount')->unsigned()->nullable();
            $table->dateTime('from');
            $table->dateTime('to')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
