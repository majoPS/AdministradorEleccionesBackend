<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frente', function (Blueprint $table) {
            $table->id('COD_FRENTE');
            $table->string('NOMBRE_FRENTE');
            $table->string('SIGLA_FRENTE');
            $table->dateTime('FECHA_INSCRIPCION');
            $table->boolean('ARCHIVADO')->default(false);
            $table->string('LOGO');
            $table->unsignedBigInteger('COD_MOTIVO')->nullable();
            $table->integer('COD_CARRERA')->nullable();
            $table->integer('COD_ELECCION')->nullable();;
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
        Schema::dropIfExists('frentes');
    }
}
