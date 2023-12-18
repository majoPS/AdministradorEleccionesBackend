<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElecciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elecciones', function (Blueprint $table) {
            $table->id('COD_ELECCION');
            $table->string('COD_ADMIN', 30)->nullable();
            $table->unsignedBigInteger('COD_FRENTE')->nullable();
            $table->unsignedBigInteger('COD_TEU')->nullable();
            $table->unsignedBigInteger('COD_COMITE')->nullable();
            $table->string('MOTIVO_ELECCION', 50);
            $table->string('TIPO_ELECCION', 255)->nullable();
            $table->date('FECHA_ELECCION');
            $table->date('FECHA_INI_CONVOCATORIA');
            $table->date('FECHA_FIN_CONVOCATORIA');
            $table->boolean('ELECCION_ACTIVA')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elecciones');
    }
}
