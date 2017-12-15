<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('start', 20)->index();
            $table->string('end', 20)->index();
            $table->integer('amount_yuan')->nullable();
            $table->dateTime('start_at')->index();
            $table->tinyInteger('num')->nullable();
            $table->string('status', 20)->default('进行中')->index();
            $table->string('plate_number', 20)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('car_brand', 20)->nullable();
            $table->string('cate', 20)->default('车找人')->index();
            $table->unsignedBigInteger('mobile');
            $table->string('go_where', 20);
            $table->text('note')->nullable();
            $table->dateTime('deleted_at')->nullable();
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
        Schema::dropIfExists('infos');
    }
}
