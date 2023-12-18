<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id('COD_MESA');
            $table->unsignedBigInteger('COD_ELECCION');
            $table->unsignedBigInteger('COD_FACULTAD')->nullable()->default(null);
            $table->unsignedBigInteger('COD_CARRERA')->nullable()->default(null);
            $table->integer('NUM_MESA');
            $table->integer('CANT_EST_MESA');
            $table->string('APELLIDOS_ESTUDIANTES', 255)->nullable()->default(null);

            $table->index('COD_ELECCION');
            $table->index('COD_FACULTAD');
            $table->index('COD_CARRERA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesas');
    }
}
