<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plcs', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('is_calibration')->default(0)->nullable();
            $table->smallInteger('is_maintenance')->default(0)->nullable();
            $table->smallInteger('d_off')->default(0)->nullable();
            $table->smallInteger('d0')->default(0)->nullable();
            $table->smallInteger('d1')->default(0)->nullable();
            $table->smallInteger('d2')->default(0)->nullable();
            $table->smallInteger('d3')->default(0)->nullable();
            $table->smallInteger('d4')->default(0)->nullable();
            $table->smallInteger('d5')->default(0)->nullable();
            $table->smallInteger('d6')->default(0)->nullable();
            $table->smallInteger('d7')->default(0)->nullable();
            $table->integer('sleep_sampling')->default(0)->nullable();
            $table->integer('sleep_blowback')->default(0)->nullable();
            $table->integer('loop_sampling')->default(0)->nullable();
            $table->integer('loop_blowback')->default(0)->nullable();
            $table->integer('sleep_default')->default(3)->nullable();
            $table->double('alarm')->default(0)->nullable();
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
        Schema::dropIfExists('plcs');
    }
}
