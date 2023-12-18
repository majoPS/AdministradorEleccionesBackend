<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidato', function (Blueprint $table) {
            $table->id('COD_CANDIDATO');
            $table->unsignedBigInteger('COD_FRENTE')->nullable()->default(null);
            $table->string('COD_CARNETIDENTIDAD', 25)->nullable()->default(null);
            $table->string('CARGO_POSTULADO', 30);
            $table->tinyInteger('HABILITADO');

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
        Schema::dropIfExists('candidatos');
    }
}
