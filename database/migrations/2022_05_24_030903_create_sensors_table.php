<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->string('code')->nullable();
            $table->string('name')->nullable()->comment('Render HTML Value');;
            $table->string('unit_formula')->nullable();
            $table->string('read_formula')->nullable();
            $table->string('write_formula')->nullable();
            $table->string('quality_standard')->nullable();
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
        Schema::dropIfExists('sensors');
    }
}
