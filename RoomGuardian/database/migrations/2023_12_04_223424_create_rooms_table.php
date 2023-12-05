<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string("name", 50);
            $table->boolean('Sensor_magnetico')->default(false);
            $table->boolean('Sensor_movimiento')->default(false);
            $table->float('Sensor_temperatura')->nullable();
            $table->float('Sensor_humedad')->nullable();
            $table->boolean('Sensor_Luz')->default(false);
            $table->float('Sensor_Voltaje')->nullable();
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
        Schema::dropIfExists('rooms');
    }
};
