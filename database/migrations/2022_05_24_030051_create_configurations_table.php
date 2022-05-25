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
            $table->string('schedule_auto_calibration')->default('1')->nullable()->comment('Separate days with comma');
            $table->smallInteger('is_calibration')->default(0)->nullable()->comment("0 = Nothing, 1 = Auto, 2 = Manual");
            $table->smallInteger('calibration_type')->default(0)->nullable()->comment('1 = Zero, 2 = Span, 0 = Nothing');
            $table->integer('a_default_zero_loop')->default(0)->nullable();
            $table->integer('a_default_span_loop')->default(0)->nullable();
            $table->integer('a_time_zero_loop')->default(0)->nullable()->comment('Second');
            $table->integer('a_time_span_loop')->default(0)->nullable()->comment('Second');
            $table->integer('a_max_span_ppm')->default(0)->nullable();
            $table->integer('m_default_zero_loop')->default(0)->nullable();
            $table->integer('m_default_span_loop')->default(0)->nullable();
            $table->integer('m_time_zero_loop')->default(0)->nullable()->comment('Second');
            $table->integer('m_time_span_loop')->default(0)->nullable()->comment('Second');
            $table->integer('m_max_span_ppm')->default(0)->nullable();
            $table->timestamp('m_start_calibration_at')->default(null)->nullable();
            $table->timestamp('m_end_calibration_at')->default(null)->nullable();
            $table->timestamp('a_start_calibration_at')->default(null)->nullable();
            $table->timestamp('a_end_calibration_at')->default(null)->nullable();
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
