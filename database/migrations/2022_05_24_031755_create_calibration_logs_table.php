<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalibrationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calibration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                ->nullable()
                ->constrained('sensors')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->double('value')->default(0)->nullable();
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
        Schema::dropIfExists('calibration_logs');
    }
}
