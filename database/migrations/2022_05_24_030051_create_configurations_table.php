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
            $table->smallInteger('is_calibration')->default(0)->nullable()->comment("0 = Nothing, 1 = Auto, 2 = Manual");
            $table->smallInteger('is_blowback')->default(5)->nullable()->comment('1 = Blowback, 0 = No');
            $table->smallInteger('calibration_type')->default(0)->nullable()->comment('1 = Zero, 2 = Span, 0 = Nothing');
            $table->smallInteger('sensor_id')->default(1)->nullable()->comment('relate with sensors table');
            $table->double('target_value')->default(null)->nullable()->comment('Target value for calibration');
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
