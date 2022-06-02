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
            $table->smallInteger('is_calibration_history')->default(0)->nullable()->comment("0 = Nothing, 1 = Auto, 2 = Manual");
            $table->smallInteger('is_calibration')->default(0)->nullable()->comment("0 = Nothing, 1 = Auto, 2 = Manual");
            $table->smallInteger('calibration_type')->default(0)->nullable()->comment('1 = Zero, 2 = Span, 0 = Nothing');
            $table->smallInteger('is_relay_open')->default(0)->nullable()->comment('1 = Zero, 2 Span, 3 = Open, 4 = Blowback, 0 = Nothing');
            $table->integer('blowback_duration')->default(5)->nullable()->comment('Duration in second');
            $table->integer('m_default_zero_loop')->default(0)->nullable();
            $table->integer('m_default_span_loop')->default(0)->nullable();
            $table->integer('m_time_zero_loop')->default(0)->nullable()->comment('Second');
            $table->integer('m_time_span_loop')->default(0)->nullable()->comment('Second');
            $table->integer('m_max_span_ppm')->default(0)->nullable();
            $table->integer('loop_count')->default(0)->nullable();
            $table->timestamp('m_start_calibration_at')->default(null)->nullable();
            $table->timestamp('m_end_calibration_at')->default(null)->nullable();
            $table->timestamp('start_blowback_at')->default(null)->nullable();
            $table->timestamp('end_blowback_at')->default(null)->nullable();
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
