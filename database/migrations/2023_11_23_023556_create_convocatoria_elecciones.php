<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvocatoriaElecciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convocatoria_elecciones', function (Blueprint $table) {
            $table->id('id_convocatoria');
            $table->date('fecha_inicio')->nullable()->default(null);
            $table->date('fecha_fin')->nullable()->default(null);
            $table->string('motivo', 255)->nullable()->default(null);
            $table->text('descripcion')->nullable()->default(null);
            $table->text('requisitos')->nullable()->default(null);
            $table->unsignedBigInteger('id_eleccion')->nullable()->default(null);
            $table->string('tipo', 50)->nullable()->default(null);
            $table->integer('candidatos')->nullable()->default(null);
            $table->string('estado', 50)->nullable()->default(null);
            $table->text('restricciones')->nullable()->default(null);
            $table->string('contacto', 100)->nullable()->default(null);
            $table->string('lugar', 100)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convocatoria_elecciones');
    }
}
