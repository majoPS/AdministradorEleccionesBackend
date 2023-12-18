<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoblacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poblacion', function (Blueprint $table) {
            $table->char('CODSIS', 25); // Cambiado a char y eliminado primary
            $table->char('CODCOMITE', 25)->nullable();
            $table->char('NOMBRE', 40);
            $table->char('CARNETIDENTIDAD', 25); // Cambiado a char
            $table->tinyInteger('ESTUDIANTE')->nullable();
            $table->tinyInteger('DOCENTE')->nullable();
            $table->char('APELLIDO', 40);
            $table->string('EMAIL',30)->nullable();

            // Añadir índice único para CODSIS si es necesario
            // $table->unique('CODSIS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poblacion');
    }
}
