<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoelectoral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procesoelectoral', function (Blueprint $table) {
            $table->char('CODPROCESOELECTORAL', 15);
            $table->char('CODADMINISTRADOR', 15)->nullable()->default(null);
            $table->char('CARGO', 100)->nullable()->default(null);
            $table->date('FECHAINICIOCONVOCATORIA')->nullable()->default(null);
            $table->date('FECHAFINCONVOCATORIA')->nullable()->default(null);
            $table->date('FECHAELECCIONES')->nullable()->default(null);
            $table->char('TIPOELECCIONES', 25)->nullable()->default(null);
            $table->char('CONVOCATORIA', 60)->nullable()->default(null);

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
        Schema::dropIfExists('procesoelectoral');
    }
}
