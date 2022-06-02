<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalibrationAvgLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calibration_avg_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                ->nullable()
                ->constrained('sensors')
                ->onUpdate('cascade')
                ->onDelete('set null');
                $table->integer('row_count')->default(1)->nullable();
                $table->double('value')->default(0)->nullable();
                $table->integer('cal_gas_ppm')->default(1)->nullable();
                $table->integer('cal_duration')->default(0)->nullable()->comment('Second');
            $table->smallInteger('calibration_type')->default(0)->nullable()->comment('1 = Zero, 2 = Span, 0 = Nothing');
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
        Schema::dropIfExists('calibration_avg_logs');
    }
}
