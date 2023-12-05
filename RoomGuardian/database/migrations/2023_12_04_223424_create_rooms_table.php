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
            $table->string("name", 50)->unique();
            $table->boolean('Sensor_magnetico')->default(false);
            $table->boolean('Sensor_movimiento')->default(false);
            $table->float('sensor_temperatura')->nullable();
            $table->float('sensor_humedad')->nullable();
            $table->boolean('sensor_luz')->default(false);
            $table->float('sensor_voltaje')->nullable();
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
