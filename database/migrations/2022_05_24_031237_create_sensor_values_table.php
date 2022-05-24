<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                ->nullable()
                ->constrained('sensors')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->double('value')->default(0)->nullable();
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
        Schema::dropIfExists('sensor_values');
    }
}
