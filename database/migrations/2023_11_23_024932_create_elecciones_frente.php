<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEleccionesFrente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elecciones_frente', function (Blueprint $table) {
            $table->unsignedBigInteger('COD_ELECCION');
            $table->unsignedBigInteger('COD_FRENTE');

            $table->primary(['COD_ELECCION', 'COD_FRENTE']);
            $table->index('COD_FRENTE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elecciones_frente');
    }
}
