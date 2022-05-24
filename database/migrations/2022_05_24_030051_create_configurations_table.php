<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('schedule_auto_calibration')->default('1')->nullable()->comment('Separate days with comma');
            $table->integer('is_calibration')->default(0)->nullable();
            $table->integer('default_zero_loop')->default(0)->nullable();
            $table->integer('default_span_loop')->default(0)->nullable();
            $table->integer('time_zero_loop')->default(0)->nullable()->comment('Second');
            $table->integer('time_span_loop')->default(0)->nullable()->comment('Second');
            $table->integer('max_span_ppm')->default(0)->nullable();
            $table->timestamp('start_calibration_at')->default(null)->nullable();
            $table->timestamp('date_and_time')->default(DB::raw('current_timestamp()'))->nullable();
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
        Schema::dropIfExists('configurations');
    }
}
