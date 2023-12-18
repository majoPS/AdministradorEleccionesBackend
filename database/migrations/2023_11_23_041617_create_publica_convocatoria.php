<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicaConvocatoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicar_convocatoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_convocatoria')->nullable()->default(null);
            $table->date('fecha_publicacion')->nullable()->default(null);
            $table->string('titulo', 255)->nullable()->default(null);
            $table->text('contenido')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publica_convocatoria');
    }
}
