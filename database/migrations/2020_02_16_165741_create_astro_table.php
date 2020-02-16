<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAstroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('astros', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 3)->comment('星座');
            $table->date('date')->comment('日期'); 
            $table->string('full')->comment('整體運勢');
            $table->string('career')->comment('事業運勢');
            $table->string('love')->comment('愛情運勢');
            $table->string('fortune')->comment('財運運勢');
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
        Schema::dropIfExists('astro');
    }
}
